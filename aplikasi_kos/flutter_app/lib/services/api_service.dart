import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/kos_model.dart';
import '../utils/url_helper.dart';

class ApiService {

  Future<List<KosModel>> getSemuaKos({
    String search = '',
    String tipe = '',
    int minHarga = 0,
    int maxHarga = 999999999,
  }) async {
    try {
      final url = Uri.parse(
        '${UrlHelper.getBaseUrl()}/kos/get_semua_kos.php?search=${Uri.encodeComponent(search)}&tipe=${Uri.encodeComponent(tipe)}&min_harga=$minHarga&max_harga=$maxHarga'
      );

      debugPrint('📍 URL: $url');

      final response = await http.get(url);

      debugPrint('📡 Status: ${response.statusCode}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          final List<dynamic> list = data['data'];
          debugPrint('✅ Jumlah data: ${list.length}');
          return list.map((item) => KosModel.fromJson(item)).toList();
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