import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:mobile_app/models/kos.dart';
import 'package:mobile_app/providers/auth_provider.dart';
import 'package:mobile_app/providers/kos_provider.dart';
import 'package:mobile_app/core/routes/app_routes.dart';

class KosCard extends StatelessWidget {
  final Kos kos;

  const KosCard({super.key, required this.kos});

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    );

    return GestureDetector(
      onTap: () {
        Navigator.pushNamed(
          context,
          AppRoutes.kosDetail,
          arguments: kos,
        );
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(15),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Image
              ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: Image.network(
                  kos.imageUrl,
                  width: 104,
                  height: 104,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) => Container(
                    width: 104,
                    height: 104,
                    decoration: BoxDecoration(
                      color: Colors.grey[100],
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(Icons.home_work_outlined, color: Colors.grey[400], size: 36),
                  ),
                ),
              ),
              const SizedBox(width: 14),
              // Details
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Text(
                            kos.name,
                            style: GoogleFonts.inter(
                              fontSize: 15,
                              fontWeight: FontWeight.bold,
                              color: Colors.black87,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        Consumer<KosProvider>(
                          builder: (context, kosProvider, child) {
                            final isFav = kosProvider.isFavorite(kos.id);
                            return GestureDetector(
                              onTap: () {
                                final auth = Provider.of<AuthProvider>(
                                  context,
                                  listen: false,
                                );
                                // Guard: only logged-in users can favorite
                                auth.performGuardedAction(
                                  action: () => kosProvider.toggleFavorite(kos.id),
                                  onUnauthenticated: () {
                                    Navigator.pushNamed(context, AppRoutes.login);
                                  },
                                );
                              },
                              child: Icon(
                                isFav ? Icons.favorite : Icons.favorite_border,
                                color: isFav ? Colors.red : Colors.grey[400],
                                size: 20,
                              ),
                            );
                          },
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Icon(Icons.location_on_outlined, size: 13, color: Colors.grey[500]),
                        const SizedBox(width: 3),
                        Expanded(
                          child: Text(
                            kos.location,
                            style: GoogleFonts.inter(
                              fontSize: 12,
                              color: Colors.grey[500],
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        // Rating badge
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                          decoration: BoxDecoration(
                            color: Colors.amber.shade50,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.star_rounded, size: 13, color: Colors.amber),
                              const SizedBox(width: 3),
                              Text(
                                kos.rating.toString(),
                                style: GoogleFonts.inter(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: Colors.black87,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 8),
                        // Type badge
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                          decoration: BoxDecoration(
                            color: const Color(0xFF0D3B66).withAlpha(18),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            kos.type,
                            style: GoogleFonts.inter(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: const Color(0xFF0D3B66),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Text(
                      '${currencyFormatter.format(kos.price)} / bulan',
                      style: GoogleFonts.inter(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF0D3B66),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
