import 'package:flutter/foundation.dart';
import 'package:mobile_app/models/blog.dart';
import 'package:mobile_app/data/dummy_data.dart';

class BlogProvider with ChangeNotifier {
  String? _selectedCategory;
  String _searchQuery = '';

  String? get selectedCategory => _selectedCategory;

  void updateCategory(String? category) {
    _selectedCategory = category == 'All' ? null : category;
    notifyListeners();
  }

  List<Blog> get filteredBlogs {
    return dummyBlogList.where((blog) {
      final matchesSearch = _searchQuery.isEmpty ||
          blog.title.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          blog.content.toLowerCase().contains(_searchQuery.toLowerCase());
      final matchesCategory =
          _selectedCategory == null || blog.category == _selectedCategory;
      return matchesSearch && matchesCategory;
    }).toList();
  }

  void updateSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }
}
