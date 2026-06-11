import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:share_plus/share_plus.dart';
import '../models/blog_model.dart';
import '../utils/url_helper.dart';

class DetailBlogScreen extends StatelessWidget {
  final BlogModel blog;

  const DetailBlogScreen({Key? key, required this.blog}) : super(key: key);

  String _getImageUrl() {
    if (blog.fotoThumbnail == null || blog.fotoThumbnail!.isEmpty) {
      return '';
    }
    return UrlHelper.getImageUrl(blog.fotoThumbnail, 'blog');
  }

  void _shareBlog() {
    final String url = 'https://mykos.com/detail_blog.php?id=${blog.idBlog}';
    final String text =
        '${blog.judul}\n\nLihat artikel selengkapnya di MyKos!\n\n$url';
    Share.share(text, subject: blog.judul);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text(
          'Detail Artikel',
          style: GoogleFonts.inter(
              fontWeight: FontWeight.w600, color: Colors.white),
        ),
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            icon: const Icon(Icons.share, color: Colors.white),
            onPressed: _shareBlog,
          ),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Gambar Thumbnail
            if (blog.fotoThumbnail != null && blog.fotoThumbnail!.isNotEmpty)
              CachedNetworkImage(
                imageUrl: _getImageUrl(),
                width: double.infinity,
                height: 250,
                fit: BoxFit.cover,
                placeholder: (context, url) => Container(
                  height: 250,
                  color: Colors.grey[200],
                  child: const Center(child: CircularProgressIndicator()),
                ),
                errorWidget: (context, url, error) => Container(
                  height: 250,
                  color: Colors.grey[200],
                  child: Icon(Icons.broken_image,
                      size: 50, color: Colors.grey[400]),
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Kategori & Tanggal
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: blog.getBadgeColor().withOpacity(0.1),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          blog.kategori,
                          style: GoogleFonts.inter(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: blog.getBadgeColor(),
                          ),
                        ),
                      ),
                      const Spacer(),
                      Icon(Icons.calendar_today,
                          size: 12, color: Colors.grey[500]),
                      const SizedBox(width: 4),
                      Text(
                        blog.formattedDate,
                        style: GoogleFonts.inter(
                            fontSize: 12, color: Colors.grey[500]),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Judul
                  Text(
                    blog.judul,
                    style: GoogleFonts.inter(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      color: const Color(0xFF1F2937),
                      height: 1.3,
                    ),
                  ),
                  const SizedBox(height: 16),

                  const Divider(),
                  const SizedBox(height: 16),

                  // Isi Konten
                  Text(
                    blog.isiKonten,
                    style: GoogleFonts.inter(
                      fontSize: 14,
                      color: Colors.grey[700],
                      height: 1.6,
                    ),
                  ),
                  const SizedBox(height: 32),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
