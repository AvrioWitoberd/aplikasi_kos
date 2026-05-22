import 'package:flutter/foundation.dart';
import '../models/kos.dart';
import '../data/dummy_data.dart';

class KosProvider with ChangeNotifier {
  List<String> _favoriteKosIds = [];
  Map<String, List<int>> ratingsData = {};

  // Filter state (used by home_screen & filter_bottom_sheet)
  String _selectedLocation = '';
  String _selectedType = '';
  List<String> _selectedPriceRanges = [];
  String _searchQuery = '';

  // Getters
  List<String> get favoriteKosIds => _favoriteKosIds;
  String get selectedLocation => _selectedLocation;
  String get selectedType => _selectedType;
  List<String> get selectedPriceRanges => List.unmodifiable(_selectedPriceRanges);

  void toggleFavorite(String id) {
    if (_favoriteKosIds.contains(id)) {
      _favoriteKosIds.remove(id);
    } else {
      _favoriteKosIds.add(id);
    }
    notifyListeners();
  }

  bool isFavorite(String id) {
    return _favoriteKosIds.contains(id);
  }

  List<Kos> get filteredKosList {
    return dummyKosList.where((kos) {
      final matchesSearch = _searchQuery.isEmpty ||
          kos.name.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          kos.location.toLowerCase().contains(_searchQuery.toLowerCase());
      final matchesLocation = _selectedLocation.isEmpty ||
          kos.location.toLowerCase().contains(_selectedLocation.toLowerCase());
      final matchesType = _selectedType.isEmpty || kos.type == _selectedType;
      final matchesPrice = _selectedPriceRanges.isEmpty ||
          _selectedPriceRanges.any((range) => _priceMatches(kos.price, range));
      return matchesSearch && matchesLocation && matchesType && matchesPrice;
    }).toList();
  }

  bool _priceMatches(double price, String range) {
    if (range == '< 500000') return price < 500000;
    if (range == '500000 - 1000000') return price >= 500000 && price <= 1000000;
    if (range == '1000000 - 1500000') return price > 1000000 && price <= 1500000;
    if (range == '1500000 - 2000000') return price > 1500000 && price <= 2000000;
    return true;
  }

  void updateSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  void applyFilters({
    required String location,
    required String type,
    required List<String> priceRanges,
  }) {
    _selectedLocation = location;
    _selectedType = type;
    _selectedPriceRanges = List.from(priceRanges);
    notifyListeners();
  }

  void clearFilters() {
    _selectedLocation = '';
    _selectedType = '';
    _selectedPriceRanges = [];
    _searchQuery = '';
    notifyListeners();
  }

  void submitRating(String kosId, int rating) {
    if (!ratingsData.containsKey(kosId)) {
      ratingsData[kosId] = [];
    }
    ratingsData[kosId]!.add(rating);

    final ratings = ratingsData[kosId]!;
    final average = ratings.reduce((a, b) => a + b) / ratings.length;

    final kosIndex = dummyKosList.indexWhere((k) => k.id == kosId);
    if (kosIndex != -1) {
      dummyKosList[kosIndex].rating = average;
      dummyKosList[kosIndex].ratingCount = ratings.length;
      notifyListeners();
    }
  }
}
