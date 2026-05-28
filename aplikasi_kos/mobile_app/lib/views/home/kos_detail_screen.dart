import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:mobile_app/models/kos.dart';
import 'package:mobile_app/providers/auth_provider.dart';
import 'package:mobile_app/providers/kos_provider.dart';
import 'package:mobile_app/core/routes/app_routes.dart';

class KosDetailScreen extends StatefulWidget {
  final Kos kos;

  const KosDetailScreen({super.key, required this.kos});

  @override
  State<KosDetailScreen> createState() => _KosDetailScreenState();
}

class _KosDetailScreenState extends State<KosDetailScreen> {
  int _selectedRating = 0;

  Future<void> _pesanSekarang(BuildContext context) async {
    final Uri url = Uri.parse(
        'https://wa.me/628123456789?text=${Uri.encodeComponent("Saya tertarik dengan kos ini")}');
    if (!await launchUrl(url)) {
      debugPrint('Could not launch $url');
    }
  }

  void _submitRating() {
    if (_selectedRating == 0) return;

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    authProvider.performGuardedAction(
      action: () {
        Provider.of<KosProvider>(context, listen: false).submitRating(
          widget.kos.id,
          _selectedRating,
        );
        setState(() {
          _selectedRating = 0;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Terima kasih atas ulasan Anda!')),
        );
      },
      onUnauthenticated: () {
        Navigator.pushNamed(context, AppRoutes.login);
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    );

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(
          "Detail Kos",
          style: GoogleFonts.inter(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Hero Image
            Image.network(
              widget.kos.imageUrl,
              width: double.infinity,
              height: 250,
              fit: BoxFit.cover,
              errorBuilder: (_, __, ___) => Container(
                height: 250,
                color: Colors.grey[200],
                child: const Center(
                  child: Icon(Icons.home, size: 50, color: Colors.grey),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Title Row
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        child: Text(
                          widget.kos.name,
                          style: GoogleFonts.inter(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: Colors.black87,
                          ),
                        ),
                      ),
                      Row(
                        children: [
                          const Icon(Icons.star, color: Colors.amber, size: 20),
                          const SizedBox(width: 4),
                          Text(
                            '${widget.kos.rating.toStringAsFixed(1)} (${widget.kos.ratingCount} ulasan)',
                            style: GoogleFonts.inter(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  // Location
                  Row(
                    children: [
                      const Icon(
                        Icons.location_on,
                        color: Colors.grey,
                        size: 16,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        widget.kos.location,
                        style: GoogleFonts.inter(
                          fontSize: 14,
                          color: Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  const Divider(),
                  const SizedBox(height: 16),

                  // Description
                  Text(
                    "Deskripsi",
                    style: GoogleFonts.inter(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    widget.kos.description,
                    style: GoogleFonts.inter(
                      fontSize: 14,
                      color: Colors.black87,
                      height: 1.5,
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Facilities
                  Text(
                    "Fasilitas",
                    style: GoogleFonts.inter(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Wrap(
                    spacing: 8.0,
                    runSpacing: 8.0,
                    children: widget.kos.facilities.map((fac) {
                      return Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 8,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade100,
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.grey.shade300),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.check_circle,
                              size: 16,
                              color: const Color(0xFF0D3B66),
                            ),
                            const SizedBox(width: 6),
                            Text(
                              fac,
                              style: GoogleFonts.inter(
                                fontSize: 13,
                                color: Colors.black87,
                              ),
                            ),
                          ],
                        ),
                      );
                    }).toList(),
                  ),

                  const SizedBox(height: 24),
                  const Divider(),
                  const SizedBox(height: 16),

                  // Rating Section
                  Text(
                    "Beri Rating",
                    style: GoogleFonts.inter(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Row(
                        children: List.generate(5, (index) {
                          return GestureDetector(
                            onTap: () {
                              setState(() {
                                _selectedRating = index + 1;
                              });
                            },
                            child: Icon(
                              index < _selectedRating
                                  ? Icons.star
                                  : Icons.star_border,
                              color: Colors.amber,
                              size: 32,
                            ),
                          );
                        }),
                      ),
                      const Spacer(),
                      ElevatedButton(
                        onPressed: _selectedRating > 0 ? _submitRating : null,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF0D3B66),
                          disabledBackgroundColor: Colors.grey[300],
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: Text(
                          "Kirim",
                          style: GoogleFonts.inter(
                            color: _selectedRating > 0 ? Colors.white : Colors.grey[600],
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(13),
              blurRadius: 10,
              offset: const Offset(0, -5),
            ),
          ],
        ),
        child: SafeArea(
          child: Row(
            children: [
              Expanded(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      "Harga",
                      style: GoogleFonts.inter(
                        fontSize: 12,
                        color: Colors.grey[600],
                      ),
                    ),
                    Text(
                      '${currencyFormatter.format(widget.kos.price)} / bulan',
                      style: GoogleFonts.inter(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF0D3B66),
                      ),
                    ),
                  ],
                ),
              ),
              ElevatedButton(
                onPressed: () => _pesanSekarang(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF0D3B66),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 24,
                    vertical: 12,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: Text(
                  "Pesan Sekarang",
                  style: GoogleFonts.inter(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
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
