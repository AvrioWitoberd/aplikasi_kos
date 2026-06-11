import 'package:flutter/material.dart';
import '../models/kos_model.dart';
import '../services/api_service.dart';

class KosProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  
  List<KosModel> _filteredKosList = [];
  bool _isLoading = false;
  bool _hasError = false;
  String _errorMessage = '';
  String _searchQuery = '';        // TAMBAHKAN INI
  String _selectedTipe = '';
  int _minHarga = 0;
  int _maxHarga = 999999999;

  // GETTERS
  List<KosModel> get filteredKosList => _filteredKosList;
  bool get isLoading => _isLoading;
  bool get hasError => _hasError;
  String get errorMessage => _errorMessage;
  String get searchQuery => _searchQuery;        // TAMBAHKAN INI
  String get selectedTipe => _selectedTipe;
  int get minHarga => _minHarga;
  int get maxHarga => _maxHarga;

  Future<void> loadKos() async {
    _isLoading = true;
    _hasError = false;
    notifyListeners();
    
    try {
      debugPrint('🔄 Loading kos with search: $_searchQuery, tipe: $_selectedTipe');
      
      final kosList = await _apiService.getSemuaKos(
        search: _searchQuery,
        tipe: _selectedTipe,
        minHarga: _minHarga,
        maxHarga: _maxHarga,
      );
      
      _filteredKosList = kosList;
      debugPrint('✅ Loaded ${_filteredKosList.length} kos');
      
    } catch (e) {
      _hasError = true;
      _errorMessage = e.toString();
      _filteredKosList = [];
      debugPrint('❌ Error loading kos: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void updateSearchQuery(String query) {
    _searchQuery = query;
    loadKos();
  }

  void applyFilters({
    String? search,
    String? tipe,
    int? minHarga,
    int? maxHarga,
  }) {
    if (search != null) _searchQuery = search;
    if (tipe != null) _selectedTipe = tipe;
    if (minHarga != null) _minHarga = minHarga;
    if (maxHarga != null) _maxHarga = maxHarga;
    
    loadKos();
  }

  void clearFilters() {
    _searchQuery = '';
    _selectedTipe = '';
    _minHarga = 0;
    _maxHarga = 999999999;
    loadKos();
  }
}