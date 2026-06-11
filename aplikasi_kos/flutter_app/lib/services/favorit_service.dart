import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/kos_model.dart';
import '../providers/auth_provider.dart';
import '../utils/url_helper.dart';

class FavoritService {

  Future<Map<String, dynamic>> toggleFavorit(int idKos, int idUser) async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/favorit/tambah.php');

      final response = await http.post(
        url,
        body: {
          'id_kos': idKos.toString(),
          'id_user': idUser.toString(), // KIRIM ID USER
        },
      );

      debugPrint('📡 Toggle Favorit Response: ${response.body}');

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('HTTP Error: ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('❌ Error toggle favorit: $e');
      throw Exception('Error: $e');
    }
  }

  Future<bool> cekStatusFavorit(int idKos, int idUser) async {
    try {
      final url = Uri.parse(
          '${UrlHelper.getBaseUrl()}/favorit/cek_status.php?id=$idKos&id_user=$idUser');

      final response = await http.get(url);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data['is_favorit'] == true;
      }
      return false;
    } catch (e) {
      debugPrint('❌ Error cek favorit: $e');
      return false;
    }
  }

  Future<List<KosModel>> getDaftarFavorit(int idUser) async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/favorit/daftar.php?id_user=$idUser');

      final response = await http.get(url);

      debugPrint('📡 Daftar Favorit Response: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          final List<dynamic> list = data['data'];
          return list.map((item) => KosModel.fromJson(item)).toList();
        } else {
          return [];
        }
      } else {
        return [];
      }
    } catch (e) {
      debugPrint('❌ Error get favorit: $e');
      return [];
    }
  }
}