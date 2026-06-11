import 'package:flutter/material.dart';
import '../models/detail_kos_model.dart';
import '../services/detail_kos_service.dart';
import '../services/favorit_service.dart';
import 'auth_provider.dart';

class DetailKosProvider extends ChangeNotifier {
  final DetailKosService _service = DetailKosService();
  final FavoritService _favoritService = FavoritService();
  
  DetailKosResponse? _data;
  bool _isLoading = false;
  bool _hasError = false;
  String _errorMessage = '';
  bool _isFavorit = false;
  bool _isFavoritLoading = false;

  DetailKosResponse? get data => _data;
  bool get isLoading => _isLoading;
  bool get hasError => _hasError;
  String get errorMessage => _errorMessage;
  bool get isFavorit => _isFavorit;
  bool get isFavoritLoading => _isFavoritLoading;

  Future<void> loadDetail(int idKos) async {
    _isLoading = true;
    _hasError = false;
    notifyListeners();
    
    try {
      final result = await _service.getDetailKos(idKos);
      _data = result;
    } catch (e) {
      _hasError = true;
      _errorMessage = e.toString();
      _data = null;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> cekStatusFavorit(int idKos, int userId) async {
    try {
      _isFavorit = await _favoritService.cekStatusFavorit(idKos, userId);
      notifyListeners();
    } catch (e) {
      debugPrint('Error cek favorit: $e');
    }
  }

  Future<bool> toggleFavorit(int idKos, int userId) async {
    _isFavoritLoading = true;
    notifyListeners();
    
    try {
      final result = await _favoritService.toggleFavorit(idKos, userId);
      if (result['status'] == 'success') {
        _isFavorit = result['action'] == 'added';
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      debugPrint('Error toggle favorit: $e');
      return false;
    } finally {
      _isFavoritLoading = false;
      notifyListeners();
    }
  }
}