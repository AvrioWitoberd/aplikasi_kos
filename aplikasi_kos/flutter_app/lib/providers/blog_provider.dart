import 'package:flutter/material.dart';
import '../models/blog_model.dart';
import '../services/blog_service.dart';

class BlogProvider extends ChangeNotifier {
  final BlogService _service = BlogService();
  
  List<BlogModel> _allBlogList = [];
  List<BlogModel> _filteredBlogList = [];
  bool _isLoading = false;
  bool _hasError = false;
  String _errorMessage = '';
  String _searchQuery = '';
  String _selectedKategori = '';

  List<BlogModel> get filteredBlogList => _filteredBlogList;
  bool get isLoading => _isLoading;
  bool get hasError => _hasError;
  String get errorMessage => _errorMessage;
  List<String> get uniqueKategori {
    final Set<String> kategoriSet = {};
    for (var blog in _allBlogList) {
      kategoriSet.add(blog.kategori);
    }
    return kategoriSet.toList();
  }

  Future<void> loadBlog() async {
    debugPrint('🟡 loadBlog() DIPANGGIL');
    _isLoading = true;
    _hasError = false;
    notifyListeners();
    
    try {
      final blogList = await _service.getAllBlog();
      debugPrint('✅ loadBlog() BERHASIL - ${blogList.length} artikel');
      _allBlogList = blogList;
      _applyFilters();
    } catch (e) {
      debugPrint('❌ loadBlog() ERROR: $e');
      _hasError = true;
      _errorMessage = e.toString();
      _filteredBlogList = [];
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void updateSearchQuery(String query) {
    _searchQuery = query;
    _applyFilters();
  }

  void updateKategori(String kategori) {
    _selectedKategori = kategori;
    _applyFilters();
  }

  void _applyFilters() {
    List<BlogModel> filtered = List.from(_allBlogList);
    
    if (_searchQuery.isNotEmpty) {
      filtered = filtered.where((blog) =>
        blog.judul.toLowerCase().contains(_searchQuery.toLowerCase())
      ).toList();
    }
    
    if (_selectedKategori.isNotEmpty) {
      filtered = filtered.where((blog) =>
        blog.kategori.toLowerCase() == _selectedKategori.toLowerCase()
      ).toList();
    }
    
    _filteredBlogList = filtered;
    notifyListeners();
  }
}