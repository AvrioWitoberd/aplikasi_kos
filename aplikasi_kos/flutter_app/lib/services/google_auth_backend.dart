import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/user_model.dart';
import '../utils/url_helper.dart';

class GoogleAuthBackend {
  Future<UserModel> loginWithGoogle(Map<String, dynamic> googleData) async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/auth/google_login.php');

      debugPrint('📍 Google Login URL: $url');
      debugPrint('📧 Email: ${googleData['email']}');
      debugPrint('👤 Name: ${googleData['nama_lengkap']}');
      debugPrint('🆔 Google ID: ${googleData['google_id']}');

      final response = await http.post(
        url,
        body: {
          'email': googleData['email'],
          'nama_lengkap': googleData['nama_lengkap'],
          'google_id': googleData['google_id'],
        },
      );

      debugPrint('📡 Response Status: ${response.statusCode}');
      debugPrint('📡 Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          debugPrint('✅ User received, parsing to model...');
          return UserModel.fromJson(data['data']);
        } else {
          throw Exception(data['message'] ?? 'Login Google gagal');
        }
      } else {
        throw Exception('HTTP Error: ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('❌ Error: $e');
      throw Exception(e.toString().replaceAll('Exception: ', ''));
    }
  }
}