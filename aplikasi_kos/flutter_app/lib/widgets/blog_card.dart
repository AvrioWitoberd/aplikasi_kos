import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/blog_model.dart';
import '../services/network_discovery_service.dart';
import '../utils/url_helper.dart';

class BlogCard extends StatelessWidget {
  final BlogModel blog;
  final VoidCallback onTap;

  const BlogCard({
    Key? key,
    required this.blog,
    required this.onTap,
  }) : super(key: key);

  String _getImageUrl() {
    if (blog.fotoThumbnail == null || blog.fotoThumbnail!.isEmpty) {
      return '';
    }
    return UrlHelper.getImageUrl(blog.fotoThumbnail, 'blog');
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: IntrinsicHeight(
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.grey.withOpacity(0.08),
                spreadRadius: 1,
                blurRadius: 10,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Gambar Thumbnail - Full tinggi (stretch)
              ClipRRect(
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(16),
                  bottomLeft: Radius.circular(16),
                ),
                child: (blog.fotoThumbnail != null &&
                        blog.fotoThumbnail!.isNotEmpty)
                    ? CachedNetworkImage(
                        imageUrl: _getImageUrl(),
                        width: 130,
                        fit: BoxFit.cover,
                        placeholder: (context, url) => Container(
                          width: 130,
                          color: Colors.grey[200],
                          child:
                              const Center(child: CircularProgressIndicator()),
                        ),
                        errorWidget: (context, url, error) => Container(
                          width: 130,
                          color: Colors.grey[200],
                          child: Icon(Icons.broken_image,
                              size: 30, color: Colors.grey[400]),
                        ),
                      )
                    : Container(
                        width: 130,
                        color: Colors.grey[200],
                        child: Icon(Icons.image_not_supported,
                            size: 30, color: Colors.grey[400]),
                      ),
              ),

              // Konten
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Kategori
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: blog.getBadgeColor().withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          blog.kategori,
                          style: GoogleFonts.inter(
                            fontSize: 9,
                            fontWeight: FontWeight.w600,
                            color: blog.getBadgeColor(),
                          ),
                        ),
                      ),
                      const SizedBox(height: 6),

                      // Judul
                      Text(
                        blog.judul,
                        style: GoogleFonts.inter(
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF1F2937),
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 4),

                      // Tanggal
                      Row(
                        children: [
                          Icon(Icons.calendar_today,
                              size: 9, color: Colors.grey[400]),
                          const SizedBox(width: 4),
                          Text(
                            blog.formattedDate,
                            style: GoogleFonts.inter(
                                fontSize: 9, color: Colors.grey[400]),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),

                      // Excerpt
                      Text(
                        blog.excerpt,
                        style: GoogleFonts.inter(
                          fontSize: 10,
                          color: Colors.grey[600],
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 8),

                      // Tombol Baca Selengkapnya
                      Row(
                        children: [
                          Text(
                            'Baca Selengkapnya',
                            style: GoogleFonts.inter(
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                              color: const Color(0xFF0D3B66),
                            ),
                          ),
                          const SizedBox(width: 4),
                          Icon(Icons.arrow_forward,
                              size: 10, color: const Color(0xFF0D3B66)),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
