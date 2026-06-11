class KosModel {
  final int idKos;
  final String namaKos;
  final String? alamatLengkap;
  final String kota;
  final String tipeKos;
  final int hargaPerBulan;
  final int jumlahKamar;
  final String? fotoUtama;
  final double rataRating;
  final int totalUlasan;

  // TAMBAHAN
  final String? deskripsi;
  final String? peraturanKos;
  final String? areaSekitarKos;
  final String? noHpKos;
  final String? noHpPemilik;

  KosModel({
    required this.idKos,
    required this.namaKos,
    this.alamatLengkap,
    required this.kota,
    required this.tipeKos,
    required this.hargaPerBulan,
    required this.jumlahKamar,
    this.fotoUtama,
    required this.rataRating,
    required this.totalUlasan,

    // TAMBAHAN
    this.deskripsi,
    this.peraturanKos,
    this.areaSekitarKos,
    this.noHpKos,
    this.noHpPemilik,
  });

  factory KosModel.fromJson(Map<String, dynamic> json) {
    return KosModel(
      idKos: _toInt(json['id_kos']),
      namaKos: _toString(json['nama_kos']),
      alamatLengkap: _nullableString(json['alamat_lengkap']),
      kota: _toString(json['kota']),
      tipeKos: _toString(
        json['tipe_kos'],
        defaultValue: 'campur',
      ),
      hargaPerBulan: _toInt(json['harga_per_bulan']),
      jumlahKamar: _toInt(json['jumlah_kamar']),
      fotoUtama: _nullableString(json['foto_utama']),
      rataRating: _toDouble(json['rata_rating']),
      totalUlasan: _toInt(json['total_ulasan']),

      // TAMBAHAN
      deskripsi: _nullableString(json['deskripsi']),
      peraturanKos: _nullableString(json['peraturan_kos']),
      areaSekitarKos: _nullableString(json['area_sekitar_kos']),
      noHpKos: _nullableString(json['no_hp_kos']),
      noHpPemilik: _nullableString(json['no_hp_pemilik']),
    );
  }

  static String _toString(
    dynamic value, {
    String defaultValue = '',
  }) {
    if (value == null) return defaultValue;
    return value.toString();
  }

  static String? _nullableString(dynamic value) {
    if (value == null) return null;

    final result = value.toString().trim();

    if (result.isEmpty || result == 'null') {
      return null;
    }

    return result;
  }

  static int _toInt(dynamic value) {
    if (value == null) return 0;

    if (value is int) return value;

    if (value is double) {
      return value.toInt();
    }

    if (value is String) {
      final parsed = double.tryParse(value);
      return parsed?.toInt() ?? 0;
    }

    return 0;
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;

    if (value is double) return value;

    if (value is int) {
      return value.toDouble();
    }

    if (value is String) {
      return double.tryParse(value) ?? 0.0;
    }

    return 0.0;
  }
}