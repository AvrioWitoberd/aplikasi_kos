import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/kos_model.dart';
import '../screens/detail_kos_screen.dart';
import 'package:provider/provider.dart';
import '../providers/kos_provider.dart';
import '../utils/url_helper.dart';

class KosCard extends StatelessWidget {
  final KosModel kos;
  final VoidCallback? onTap;

  const KosCard({
    Key? key,
    required this.kos,
    this.onTap,
  }) : super(key: key);

  String _formatHarga(int harga) {
    return harga.toString().replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (Match m) => '${m[1]}.',
        );
  }

  String _getImageUrl() {
    if (kos.fotoUtama == null || kos.fotoUtama!.isEmpty) {
      return '';
    }
    final url = UrlHelper.getImageUrl(kos.fotoUtama, 'foto_kos');
    debugPrint('📸 Image URL: $url'); // DEBUG
    return url;
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap ??
          () async {
            final result = await Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => DetailKosScreen(idKos: kos.idKos),
              ),
            );

            if (result == 'rating_updated') {
              context.read<KosProvider>().loadKos();
            }
          },
      child: Container(
        margin: const EdgeInsets.only(bottom: 16),
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
        child: IntrinsicHeight(
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // BAGIAN FOTO
              ClipRRect(
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(16),
                  bottomLeft: Radius.circular(16),
                ),
                child: SizedBox(
                  width: 110,
                  child: kos.fotoUtama != null && kos.fotoUtama!.isNotEmpty
                      ? Image.network(
                          _getImageUrl(),
                          width: 110,
                          fit: BoxFit.cover,
                          loadingBuilder: (context, child, loadingProgress) {
                            if (loadingProgress == null) return child;
                            return Container(
                              color: Colors.grey[100],
                              child: const Center(
                                child: SizedBox(
                                  width: 24,
                                  height: 24,
                                  child: CircularProgressIndicator(strokeWidth: 2),
                                ),
                              ),
                            );
                          },
                          errorBuilder: (context, error, stackTrace) {

                            debugPrint('❌ URL GAGAL: ${_getImageUrl()}');
                            debugPrint('❌ ERROR: $error');

                            return Container(
                              color: Colors.grey[100],
                              child: const Icon(
                                Icons.broken_image,
                                size: 35,
                              ),
                            );
                          },
                        )
                      : Container(
                          color: Colors.grey[100],
                          child: Icon(Icons.home,
                              size: 35, color: Colors.grey[400]),
                        ),
                ),
              ),
              // SISA KONTEN (SAMA SEPERTI SEBELUMNYA)
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(10),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  kos.namaKos,
                                  style: GoogleFonts.inter(
                                    fontSize: 14,
                                    fontWeight: FontWeight.bold,
                                    color: const Color(0xFF1F2937),
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                              const SizedBox(width: 4),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 8, vertical: 3),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF0FDF4),
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: Text(
                                  kos.tipeKos,
                                  style: GoogleFonts.inter(
                                    fontSize: 10,
                                    fontWeight: FontWeight.w600,
                                    color: const Color(0xFF16A34A),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              const Icon(Icons.location_on,
                                  size: 12, color: Colors.grey),
                              const SizedBox(width: 4),
                              Expanded(
                                child: Text(
                                  kos.alamatLengkap ?? kos.kota,
                                  style: GoogleFonts.inter(
                                    fontSize: 11,
                                    color: Colors.grey[500],
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          ...List.generate(5, (index) {
                            if (index < kos.rataRating.floor()) {
                              return const Icon(Icons.star,
                                  size: 13, color: Color(0xFFFFC107));
                            } else if (index < kos.rataRating.ceil() &&
                                kos.rataRating % 1 > 0) {
                              return const Icon(Icons.star_half,
                                  size: 13, color: Color(0xFFFFC107));
                            } else {
                              return const Icon(Icons.star_border,
                                  size: 13, color: Color(0xFFFFC107));
                            }
                          }),
                          const SizedBox(width: 4),
                          Text(
                            kos.rataRating.toStringAsFixed(1),
                            style: GoogleFonts.inter(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: const Color(0xFF1F2937),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Mulai dari',
                                style: GoogleFonts.inter(
                                    fontSize: 10, color: Colors.grey[400]),
                              ),
                              Text(
                                'Rp ${_formatHarga(kos.hargaPerBulan)}',
                                style: GoogleFonts.inter(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                  color: const Color(0xFF0D3B66),
                                ),
                              ),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: kos.jumlahKamar > 0
                                  ? const Color(0xFFE0F2FE)
                                  : const Color(0xFFFFF4E5),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Text(
                              kos.jumlahKamar > 0
                                  ? '${kos.jumlahKamar} Kamar'
                                  : 'Penuh',
                              style: GoogleFonts.inter(
                                fontSize: 10,
                                fontWeight: FontWeight.w600,
                                color: kos.jumlahKamar > 0
                                    ? const Color(0xFF0284C7)
                                    : const Color(0xFFEA580C),
                              ),
                            ),
                          ),
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