import 'package:flutter/material.dart';
import '../models/kos_model.dart';
import '../services/favorit_service.dart';
import 'auth_provider.dart';

class FavoritProvider extends ChangeNotifier {
  final FavoritService _service = FavoritService();
  
  List<KosModel> _favoritList = [];
  bool _isLoading = false;
  bool _hasError = false;

  List<KosModel> get favoritList => _favoritList;
  bool get isLoading => _isLoading;
  bool get hasError => _hasError;

  Future<void> loadFavorit() async {
    _isLoading = true;
    _hasError = false;
    notifyListeners();
    
    try {
      // Ambil userId dari AuthProvider
      // Untuk sementara, load favorit akan dipanggil dengan context di screen
      _favoritList = [];
    } catch (e) {
      _hasError = true;
      _favoritList = [];
      debugPrint('❌ Error load favorit: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
  
  Future<void> loadFavoritWithUserId(int userId) async {
    _isLoading = true;
    _hasError = false;
    notifyListeners();
    
    try {
      _favoritList = await _service.getDaftarFavorit(userId);
      debugPrint('✅ Loaded ${_favoritList.length} favorit items');
    } catch (e) {
      _hasError = true;
      _favoritList = [];
      debugPrint('❌ Error load favorit: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void removeFavorit(int idKos) {
    _favoritList.removeWhere((kos) => kos.idKos == idKos);
    notifyListeners();
  }
}