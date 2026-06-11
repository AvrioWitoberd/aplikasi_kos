import 'package:flutter/material.dart';
import '../models/privacy_model.dart';
import '../services/privacy_service.dart';

class PrivacyProvider extends ChangeNotifier {
  final PrivacyService _privacyService = PrivacyService();
  
  PrivacyData? _privacyData;
  bool _isLoading = false;
  bool _hasError = false;
  String _errorMessage = '';

  PrivacyData? get privacyData => _privacyData;
  bool get isLoading => _isLoading;
  bool get hasError => _hasError;
  String get errorMessage => _errorMessage;

  Future<void> loadPrivacy() async {
    _isLoading = true;
    _hasError = false;
    notifyListeners();
    
    try {
      debugPrint('🔄 Loading kebijakan privasi...');
      
      final data = await _privacyService.getKebijakanPrivasi();
      _privacyData = data;
      
      debugPrint('✅ Loaded ${data.sections.length} sections');
      
    } catch (e) {
      _hasError = true;
      _errorMessage = e.toString();
      _privacyData = null;
      debugPrint('❌ Error: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}