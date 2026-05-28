import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:mobile_app/data/dummy_data.dart';
import 'package:mobile_app/providers/kos_provider.dart';
import 'package:mobile_app/widgets/kos_card.dart';

class FavoriteScreen extends StatelessWidget {
  const FavoriteScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        foregroundColor: Colors.white,
        title: Text(
          "Kos Favorit Saya",
          style: GoogleFonts.inter(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
      body: Consumer<KosProvider>(
        builder: (context, provider, child) {
          final favoriteKosIds = provider.favoriteKosIds;
          final favoriteKosList = dummyKosList
              .where((k) => favoriteKosIds.contains(k.id))
              .toList();

          if (favoriteKosList.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.favorite_border,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    "Belum ada kos favorit",
                    style: GoogleFonts.inter(
                      fontSize: 16,
                      color: Colors.grey[600],
                    ),
                  ),
                ],
              ),
            );
          }

          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: ListView.builder(
              padding: const EdgeInsets.only(top: 16, bottom: 20),
              itemCount: favoriteKosList.length,
              itemBuilder: (context, index) {
                return KosCard(kos: favoriteKosList[index]);
              },
            ),
          );
        },
      ),
    );
  }
}
