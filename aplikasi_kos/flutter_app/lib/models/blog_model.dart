import 'package:flutter/material.dart';

class BlogModel {
  final int idBlog;
  final String judul;
  final String? fotoThumbnail;
  final String kategori;
  final String isiKonten;
  final DateTime tglDibuat;

  BlogModel({
    required this.idBlog,
    required this.judul,
    this.fotoThumbnail,
    required this.kategori,
    required this.isiKonten,
    required this.tglDibuat,
  });

  factory BlogModel.fromJson(Map<String, dynamic> json) {
    return BlogModel(
      idBlog: json['id_blog'] as int? ?? 0,
      judul: json['judul']?.toString() ?? '',
      fotoThumbnail: json['foto_thumbnail']?.toString(),
      kategori: json['kategori']?.toString() ?? 'Umum',
      isiKonten: json['isi_konten']?.toString() ?? '',
      tglDibuat: DateTime.tryParse(json['tgl_dibuat']?.toString() ?? '') ?? DateTime.now(),
    );
  }

  String get formattedDate {
    final bulan = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return '${tglDibuat.day} ${bulan[tglDibuat.month - 1]} ${tglDibuat.year}';
  }

  String get excerpt {
    if (isiKonten.length <= 100) return isiKonten;
    return '${isiKonten.substring(0, 100)}...';
  }

  Color getBadgeColor() {
    switch (kategori.toLowerCase()) {
      case 'news':
        return const Color(0xFF0D3B66);
      case 'education':
        return const Color(0xFF16A34A);
      case 'sport':
        return const Color(0xFFEA580C);
      case 'info kos':
        return const Color(0xFF0284C7);
      default:
        return const Color(0xFF6B7280);
    }
  }
}