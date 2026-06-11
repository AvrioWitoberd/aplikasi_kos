import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/detail_kos_model.dart';
import '../utils/url_helper.dart';

class DetailKosService {
  Future<DetailKosResponse> getDetailKos(int idKos) async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/kos/get_detail_kos.php?id=$idKos');

      debugPrint('📍 Request URL: $url');

      final response = await http.get(url);

      debugPrint('📡 Response Status: ${response.statusCode}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          return DetailKosResponse.fromJson(data['data']);
        } else {
          throw Exception(data['message'] ?? 'Gagal memuat data');
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