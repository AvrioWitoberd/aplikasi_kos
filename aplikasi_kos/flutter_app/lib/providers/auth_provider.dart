import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';
import '../services/google_auth_service.dart';
import '../services/google_auth_backend.dart';
import 'dart:convert';

class AuthProvider extends ChangeNotifier {
  final AuthService _authService = AuthService();
  final GoogleAuthService _googleAuth = GoogleAuthService();
  final GoogleAuthBackend _googleBackend = GoogleAuthBackend();
  
  UserModel? _user;
  bool _isLoading = false;
  String _errorMessage = '';

  UserModel? get user => _user;
  bool get isLoggedIn => _user != null;
  bool get isLoading => _isLoading;
  String get errorMessage => _errorMessage;
  String get nama => _user?.namaLengkap ?? '';
  String get email => _user?.email ?? '';
  String get role => _user?.role ?? '';
  int? get userId => _user?.idUser;

  Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString('user');
    if (userJson != null) {
      try {
        final Map<String, dynamic> userData = json.decode(userJson);
        _user = UserModel.fromJson(userData);
        notifyListeners();
        debugPrint('✅ Session loaded: ${_user?.namaLengkap}');
      } catch (e) {
        debugPrint('❌ Error loading session: $e');
      }
    }
  }

  Future<void> _saveSession(UserModel user) async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = json.encode({
      'id_user': user.idUser,
      'nama_lengkap': user.namaLengkap,
      'email': user.email,
      'no_hp': user.noHp,
      'role': user.role,
    });
    await prefs.setString('user', userJson);
    debugPrint('✅ Session saved');
  }

  Future<void> _clearSession() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user');
    debugPrint('✅ Session cleared');
  }

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();
    
    try {
      final user = await _authService.loginPencari(email, password);
      _user = user;
      await _saveSession(user);
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> register(String namaLengkap, String noHp, String email, String password) async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();
    
    try {
      final user = await _authService.registerPencari(namaLengkap, noHp, email, password);
      _user = user;
      await _saveSession(user);
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> loginWithGoogle() async {
    _isLoading = true;
    _errorMessage = '';
    notifyListeners();
    
    try {
      final googleResult = await _googleAuth.signInWithGoogle();
      
      if (googleResult['status'] != 'success') {
        _errorMessage = googleResult['message'] ?? 'Login Google gagal';
        _isLoading = false;
        notifyListeners();
        return false;
      }
      
      final user = await _googleBackend.loginWithGoogle(googleResult['data']);
      _user = user;
      await _saveSession(user);
      _isLoading = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Future<void> logout() async {
  //   await _googleAuth.signOut();
  //   _user = null;
  //   await _clearSession();
  //   notifyListeners();
  // }

  Future<void> logout() async {
    await _googleAuth.signOut();

    // Logout session PHP
    await _authService.logoutServer();

    _user = null;

    await _clearSession();

    notifyListeners();
  }

  void clearError() {
    _errorMessage = '';
    notifyListeners();
  }
}