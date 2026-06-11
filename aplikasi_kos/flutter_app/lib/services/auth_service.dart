import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/user_model.dart';
import '../utils/url_helper.dart';

class AuthService {
  Future<void> logoutServer() async {
    try {
      final url =
          Uri.parse('${UrlHelper.getBaseUrl()}/auth/logout.php');

      await http.post(url);
    } catch (e) {
      debugPrint("Logout server error: $e");
    }
  }

  Future<UserModel> loginPencari(String email, String password) async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/auth/login_pencari.php');

      debugPrint('📍 Login URL: $url');

      final response = await http.post(
        url,
        body: {
          'email': email,
          'password': password,
        },
      );

      debugPrint('📡 Response Status: ${response.statusCode}');
      debugPrint('📡 Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          return UserModel.fromJson(data['data']);
        } else {
          throw Exception(data['message'] ?? 'Login gagal');
        }
      } else {
        throw Exception('HTTP Error: ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('❌ Error: $e');
      throw Exception(e.toString().replaceAll('Exception: ', ''));
    }
  }

  Future<UserModel> registerPencari(
      String namaLengkap, String noHp, String email, String password) async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/auth/register_pencari.php');

      final response = await http.post(
        url,
        body: {
          'nama_lengkap': namaLengkap,
          'no_hp': noHp,
          'email': email,
          'password': password,
        },
      );

      debugPrint('📡 Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          return UserModel.fromJson(data['data']);
        } else {
          throw Exception(data['message'] ?? 'Registrasi gagal');
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