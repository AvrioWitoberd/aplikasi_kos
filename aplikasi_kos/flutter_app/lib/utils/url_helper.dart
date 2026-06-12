class UrlHelper {
  // IP yang benar (sesuai yang sudah terbukti bekerja)
  static const String baseIp = 'http://192.168.69.78';
  static const String baseUrl = '$baseIp/aplikasi_kos/aplikasi_kos/backend_api';
  static const String uploadsUrl = '$baseIp/aplikasi_kos/aplikasi_kos/uploads';
  static const String webUrl =
      '$baseIp/aplikasi_kos/aplikasi_kos/web_dashboard';

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
