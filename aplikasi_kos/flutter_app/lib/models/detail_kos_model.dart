class DetailKosModel {
  final int idKos;
  final String namaKos;
  final String? alamatLengkap;
  final String kota;
  final String tipeKos;
  final int hargaPerBulan;
  final int jumlahKamar;
  final String? deskripsi;
  final String? fasilitasUtama;
  final String? peraturanKos;
  final String? areaSekitarKos;
  final String? linkMaps;
  final String? noHpKos;        // DARI TABEL KOS
  final String? noHpPemilik;    // DARI TABEL USERS (fallback)
  final String? namaPemilik;
  final double rataRating;
  final int totalUlasan;

  DetailKosModel({
    required this.idKos,
    required this.namaKos,
    this.alamatLengkap,
    required this.kota,
    required this.tipeKos,
    required this.hargaPerBulan,
    required this.jumlahKamar,
    this.deskripsi,
    this.fasilitasUtama,
    this.peraturanKos,
    this.areaSekitarKos,
    this.linkMaps,
    this.noHpKos,
    this.noHpPemilik,
    this.namaPemilik,
    required this.rataRating,
    required this.totalUlasan,
  });

  factory DetailKosModel.fromJson(Map<String, dynamic> json) {
    return DetailKosModel(
      idKos: json['id_kos'] as int? ?? 0,
      namaKos: json['nama_kos']?.toString() ?? '',
      alamatLengkap: json['alamat_lengkap']?.toString(),
      kota: json['kota']?.toString() ?? '',
      tipeKos: json['tipe_kos']?.toString() ?? 'campur',
      hargaPerBulan: _toInt(json['harga_per_bulan']),
      jumlahKamar: _toInt(json['jumlah_kamar']),
      deskripsi: json['deskripsi']?.toString(),
      fasilitasUtama: json['fasilitas_utama']?.toString(),
      peraturanKos: json['peraturan_kos']?.toString(),
      areaSekitarKos: json['area_sekitar_kos']?.toString(),
      linkMaps: json['link_maps']?.toString(),
      noHpKos: json['no_hp_kos']?.toString(),          // PRIORITAS
      noHpPemilik: json['no_hp_pemilik']?.toString(),  // FALLBACK
      namaPemilik: json['nama_pemilik']?.toString(),
      rataRating: _toDouble(json['rata_rating']),
      totalUlasan: _toInt(json['total_ulasan']),
    );
  }

  static int _toInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    if (value is String) return double.tryParse(value)?.toInt() ?? 0;
    return 0;
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }
}

class DetailKosResponse {
  final DetailKosModel kos;
  final List<String> fotoList;
  final List<String> fasilitasList;
  final String? embedMapUrl;
  final String? directMapUrl;

  DetailKosResponse({
    required this.kos,
    required this.fotoList,
    required this.fasilitasList,
    this.embedMapUrl,
    this.directMapUrl,
  });

  factory DetailKosResponse.fromJson(Map<String, dynamic> json) {
    final kosData = json['kos'] as Map<String, dynamic>;
    final ratingData = json['rating'] as Map<String, dynamic>? ?? {};
    final mapsData = json['maps'] as Map<String, dynamic>? ?? {};

    // Gabungkan rating ke kosData
    kosData['rata_rating'] = ratingData['rata_rating'];
    kosData['total_ulasan'] = ratingData['total_ulasan'];

    return DetailKosResponse(
      kos: DetailKosModel.fromJson(kosData),
      fotoList: (json['fotos'] as List?)?.map((e) => e.toString()).toList() ?? [],
      fasilitasList: (json['fasilitas'] as List?)?.map((e) => e.toString()).toList() ?? [],
      embedMapUrl: mapsData['embed_url']?.toString(),
      directMapUrl: mapsData['direct_url']?.toString(),
    );
  }
}