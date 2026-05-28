import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:mobile_app/providers/kos_provider.dart';
import 'package:mobile_app/widgets/filter_bottom_sheet.dart';
import 'package:mobile_app/widgets/kos_card.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:mobile_app/views/auth/role_selection_screen.dart';
import 'package:mobile_app/providers/auth_provider.dart';
import 'package:mobile_app/views/main_navigation.dart';
import 'package:mobile_app/views/home/privacy_screen.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(
          "Keluar",
          style: GoogleFonts.inter(fontWeight: FontWeight.bold),
        ),
        content: Text(
          "Apakah anda yakin ingin keluar dari akun?",
          style: GoogleFonts.inter(),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: Text("Batal", style: GoogleFonts.inter(color: Colors.grey)),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              context.read<AuthProvider>().logout();
              Navigator.pushAndRemoveUntil(
                context,
                MaterialPageRoute(builder: (_) => const MainNavigationScreen(initialIndex: 0)),
                (route) => false,
              );
            },
            child: Text("Keluar", style: GoogleFonts.inter(color: Colors.red)),
          ),
        ],
      ),
    );
  }

  void _showFilterBottomSheet(BuildContext context) {
    final provider = context.read<KosProvider>();
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return FilterBottomSheet(
          initialLocation: provider.selectedLocation,
          initialType: provider.selectedType,
          initialPriceRanges: provider.selectedPriceRanges,
          onApply: (location, type, priceRanges) {
            provider.applyFilters(
              location: location,
              type: type,
              priceRanges: priceRanges,
            );
          },
          onClear: () {
            provider.clearFilters();
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        foregroundColor: Colors.white,
        title: Text(
          "My Kos",
          style: GoogleFonts.inter(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
        leading: const Icon(Icons.home_work_rounded, color: Colors.white),
        actions: [
          Consumer<AuthProvider>(
            builder: (context, auth, _) {
              return PopupMenuButton<String>(
                icon: const Icon(Icons.more_vert, color: Colors.white),
                onSelected: (value) async {
                  if (value == 'pusat_bantuan') {
                    final Uri url = Uri.parse(
                        'https://wa.me/6285784649183?text=${Uri.encodeComponent("Halo admin, saya butuh bantuan terkait aplikasi My Kos")}');
                    launchUrl(url, mode: LaunchMode.externalApplication);
                  } else if (value == 'kebijakan_privasi') {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const PrivacyScreen()),
                    );
                  } else if (value == 'keluar') {
                    _showLogoutDialog(context);
                  } else if (value == 'login') {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const RoleSelectionScreen()),
                    );
                  }
                },
                itemBuilder: (BuildContext context) => [
                  const PopupMenuItem(
                    value: 'pusat_bantuan',
                    child: Text('Pusat Bantuan'),
                  ),
                  const PopupMenuItem(
                    value: 'kebijakan_privasi',
                    child: Text('Kebijakan Privasi'),
                  ),
                  if (auth.isLoggedIn)
                    const PopupMenuItem(
                      value: 'keluar',
                      child: Text(
                        'Keluar / Logout',
                        style: TextStyle(color: Colors.red),
                      ),
                    )
                  else
                    const PopupMenuItem(
                      value: 'login',
                      child: Text(
                        'Login',
                        style: TextStyle(color: Color(0xFF0D3B66)),
                      ),
                    ),
                ],
              );
            },
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        child: Column(
          children: [
            const SizedBox(height: 16),
            // Search Bar and Filter
            Row(
              children: [
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: Colors.grey.shade300),
                    ),
                    child: TextField(
                      onChanged: (value) {
                        context.read<KosProvider>().updateSearchQuery(value);
                      },
                      decoration: InputDecoration(
                        hintText: "Cari kost anda",
                        hintStyle: GoogleFonts.inter(color: Colors.grey),
                        prefixIcon: const Icon(
                          Icons.search,
                          color: Colors.grey,
                        ),
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(
                          vertical: 14,
                        ),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Container(
                  decoration: BoxDecoration(
                    color: const Color(0xFF0D3B66),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: IconButton(
                    icon: const Icon(Icons.tune, color: Colors.white),
                    onPressed: () => _showFilterBottomSheet(context),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Expanded(
              child: Consumer<KosProvider>(
                builder: (context, provider, child) {
                  final kosList = provider.filteredKosList;
                  if (kosList.isEmpty) {
                    return Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.search_off_rounded,
                            size: 72,
                            color: Colors.grey[350],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            'Kos tidak ditemukan',
                            style: GoogleFonts.inter(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                              color: Colors.grey[500],
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'Coba ubah kata kunci atau filter pencarian',
                            style: GoogleFonts.inter(
                              fontSize: 13,
                              color: Colors.grey[400],
                            ),
                            textAlign: TextAlign.center,
                          ),
                        ],
                      ),
                    );
                  }
                  return ListView.builder(
                    padding: const EdgeInsets.only(bottom: 20),
                    itemCount: kosList.length,
                    itemBuilder: (context, index) {
                      final kos = kosList[index];
                      return KosCard(kos: kos);
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}
