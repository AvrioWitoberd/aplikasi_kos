import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/blog_model.dart';
import '../utils/url_helper.dart';

class BlogService {

  Future<List<BlogModel>> getAllBlog() async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/blog/get_blog.php');

      debugPrint('📍 Request URL: $url');

      final response = await http.get(url);

      debugPrint('📡 Response Status: ${response.statusCode}');
      debugPrint('📡 Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          final List<dynamic> list = data['data'];
          debugPrint('✅ Berhasil load ${list.length} artikel blog');
          return list.map((item) => BlogModel.fromJson(item)).toList();
        } else {
          throw Exception(data['message'] ?? 'Gagal memuat data blog');
        }
      } else {
        throw Exception('HTTP Error: ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('❌ Error: $e');
      throw Exception('Error: $e');
    }
  }

  Future<BlogModel> getDetailBlog(int idBlog) async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/blog/get_detail_blog.php?id=$idBlog');

      debugPrint('📍 Request URL: $url');

      final response = await http.get(url);

      debugPrint('📡 Response Status: ${response.statusCode}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          return BlogModel.fromJson(data['data']);
        } else {
          throw Exception(data['message'] ?? 'Gagal memuat detail blog');
        }
      } else {
        throw Exception('HTTP Error: ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('❌ Error: $e');
      throw Exception('Error: $e');
    }
  }
}