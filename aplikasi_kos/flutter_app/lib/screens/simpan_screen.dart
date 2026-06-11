import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../providers/favorit_provider.dart';
import '../widgets/kos_card.dart';
import 'detail_kos_screen.dart';
import 'profile_screen.dart';

class SimpanScreen extends StatefulWidget {
  const SimpanScreen({Key? key}) : super(key: key);

  @override
  State<SimpanScreen> createState() => _SimpanScreenState();
}

class _SimpanScreenState extends State<SimpanScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadFavorit();
    });
  }

  Future<void> _loadFavorit() async {
    final auth = Provider.of<AuthProvider>(context, listen: false);
    if (auth.isLoggedIn && auth.userId != null) {
      final favoritProvider = Provider.of<FavoritProvider>(context, listen: false);
      await favoritProvider.loadFavoritWithUserId(auth.userId!);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text(
          'Kos Tersimpan',
          style: GoogleFonts.inter(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        automaticallyImplyLeading: false,
      ),
      body: Consumer<AuthProvider>(
        builder: (context, auth, child) {
          if (!auth.isLoggedIn) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.bookmark_border, size: 80, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  Text(
                    'Login untuk melihat kos tersimpan',
                    style: GoogleFonts.inter(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const ProfileScreen(showBackButton: true),
                        ),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0D3B66),
                    ),
                    child: Text('Login', style: GoogleFonts.inter(color: Colors.white)),
                  ),
                ],
              ),
            );
          }

          return Consumer<FavoritProvider>(
            builder: (context, favoritProvider, child) {
              if (favoritProvider.isLoading) {
                return const Center(
                  child: CircularProgressIndicator(color: Color(0xFF0D3B66)),
                );
              }

              if (favoritProvider.favoritList.isEmpty) {
                return Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.bookmark_border, size: 80, color: Colors.grey[400]),
                      const SizedBox(height: 16),
                      Text(
                        'Belum ada kos tersimpan',
                        style: GoogleFonts.inter(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                          color: Colors.grey[600],
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Klik ikon bookmark di detail kos untuk menyimpan',
                        style: GoogleFonts.inter(
                          fontSize: 13,
                          color: Colors.grey[500],
                        ),
                      ),
                    ],
                  ),
                );
              }

              return ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: favoritProvider.favoritList.length,
                itemBuilder: (context, index) {
                  final kos = favoritProvider.favoritList[index];
                  return KosCard(
                    kos: kos,
                    onTap: () async {
                      // Navigasi ke detail dan tunggu hasilnya
                      final result = await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => DetailKosScreen(idKos: kos.idKos),
                        ),
                      );
                      
                      // Jika ada perubahan favorit, reload data
                      if (result == 'added' || result == 'removed') {
                        await _loadFavorit();
                      }
                    },
                  );
                },
              );
            },
          );
        },
      ),
    );
  }
}