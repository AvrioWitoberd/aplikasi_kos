import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/privacy_model.dart';
import '../utils/url_helper.dart';

class PrivacyService {

  Future<PrivacyData> getKebijakanPrivasi() async {
    try {
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/kebijakan_privasi/get_kebijakan.php');

      debugPrint('📍 Request URL: $url');

      final response = await http.get(url);

      debugPrint('📡 Response Status: ${response.statusCode}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          final String introText = data['intro_text']?.toString() ?? '';
          final List<dynamic> sectionsData = data['data'] ?? [];

          final sections = sectionsData
              .where((item) =>
                  item['judul_section'] != null &&
                  item['judul_section'].toString().isNotEmpty)
              .map((item) => PrivacySection.fromJson(item))
              .toList();

          return PrivacyData(
            introText: introText,
            sections: sections,
          );
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