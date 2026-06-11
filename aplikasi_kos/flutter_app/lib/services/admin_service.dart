import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../utils/url_helper.dart';

class AdminService {
  // Fungsi untuk membersihkan nomor WhatsApp
  String _formatWaNumber(String number) {
    // Hapus semua karakter non-digit
    String cleaned = number.replaceAll(RegExp(r'[^0-9]'), '');

    debugPrint('📞 Raw number: $number, Cleaned: $cleaned');

    // Jika dimulai dengan 0, ganti dengan 62
    if (cleaned.startsWith('0')) {
      cleaned = '62${cleaned.substring(1)}';
    }
    // Jika tidak dimulai dengan 62, tambahkan 62
    else if (!cleaned.startsWith('62')) {
      cleaned = '62$cleaned';
    }

    debugPrint('📞 Formatted number: $cleaned');
    return cleaned;
  }

  Future<String> getAdminWaNumber() async {
    try {
      // PAKAI URLHELPER LANGSUNG
      final url = Uri.parse('${UrlHelper.getBaseUrl()}/admin/get_admin_wa.php');

      debugPrint('📍 Request Admin WA URL: $url');

      final response = await http.get(url);

      debugPrint('📡 Response Status: ${response.statusCode}');
      debugPrint('📡 Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['status'] == 'success') {
          String rawNumber = data['no_hp']?.toString() ?? '089676524908';
          debugPrint('📞 Raw number from API: $rawNumber');
          return _formatWaNumber(rawNumber);
        } else {
          debugPrint('📞 Fallback to default number');
          return _formatWaNumber('089676524908');
        }
      } else {
        debugPrint('📞 Fallback to default number (HTTP error)');
        return _formatWaNumber('089676524908');
      }
    } catch (e) {
      debugPrint('❌ Error get admin WA: $e');
      return _formatWaNumber('089676524908');
    }
  }
}