import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../utils/url_helper.dart';

class NetworkDiscoveryService extends ChangeNotifier {
  String? _cachedServerUrl;
  bool _isDiscovering = false;

  bool get isDiscovering => _isDiscovering;
  String? get serverUrl => _cachedServerUrl;

  Future<String?> discoverServer() async {
    // Langsung pakai URL dari UrlHelper
    final String knownUrl = UrlHelper.getBaseUrl();

    debugPrint('🔍 Mencoba koneksi ke: $knownUrl');

    if (await _testConnection(knownUrl)) {
      _cachedServerUrl = knownUrl;
      debugPrint('✅ Server ditemukan di: $knownUrl');
      return knownUrl;
    }

    debugPrint('❌ Server tidak ditemukan');
    return null;
  }

  Future<bool> _testConnection(String baseUrl) async {
    try {
      final url = Uri.parse('$baseUrl/kos/get_semua_kos.php');
      final response = await http.get(url).timeout(const Duration(seconds: 3));

      if (response.statusCode == 200) {
        try {
          final data = json.decode(response.body);
          if (data['status'] == 'success') {
            return true;
          }
        } catch (e) {
          // Bukan JSON
        }
      }
    } catch (e) {
      debugPrint('❌ Connection error: $e');
    }
    return false;
  }

  String getBaseUrl() {
    return _cachedServerUrl ?? UrlHelper.getBaseUrl();
  }

  void resetCache() {
    _cachedServerUrl = null;
    notifyListeners();
  }
}
