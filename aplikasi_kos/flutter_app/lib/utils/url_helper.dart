class UrlHelper {
  // IP yang benar (sesuai yang sudah terbukti bekerja)
  static const String baseIp = 'http://192.168.100.14';
  static const String baseUrl = '$baseIp/pbl_mykos/backend_api';
  static const String uploadsUrl = '$baseIp/pbl_mykos/uploads';
  static const String webUrl = '$baseIp/pbl_mykos/web_dashboard';

  static String getImageUrl(String? path, String folder) {
    if (path == null || path.isEmpty) return '';
    return '$uploadsUrl/$folder/$path';
  }

  static String getApiUrl(String endpoint) {
    return '$baseUrl/$endpoint';
  }

  static String getBaseUrl() {
    return baseUrl;
  }
}
