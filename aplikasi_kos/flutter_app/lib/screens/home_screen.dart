import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/kos_provider.dart';
import '../widgets/kos_card.dart';
import '../widgets/filter_bottom_sheet.dart';
import 'privacy_screen.dart';
import '../services/admin_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final AdminService _adminService = AdminService();
  bool _isLoadingWa = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<KosProvider>().loadKos();
    });
  }

Future<void> _openWhatsApp() async {
  setState(() {
    _isLoadingWa = true;
  });
  
  try {
    final waNumber = await _adminService.getAdminWaNumber();
    final url = Uri.parse('https://wa.me/$waNumber?text=Halo%20admin%20MyKos,%20saya%20butuh%20bantuan');
    
    debugPrint('📍 Opening WhatsApp URL: $url');
    
    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
      debugPrint('✅ WhatsApp opened successfully');
    } else {
      debugPrint('❌ Cannot launch URL');
      _showErrorDialog('Tidak dapat membuka WhatsApp');
    }
  } catch (e) {
    debugPrint('❌ Error: $e');
    _showErrorDialog('Error: $e');
  } finally {
    if (mounted) {
      setState(() {
        _isLoadingWa = false;
      });
    }
  }
}

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Info'),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('OK'),
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
          initialSearch: provider.searchQuery,
          initialTipe: provider.selectedTipe,
          initialMinHarga: provider.minHarga,
          initialMaxHarga: provider.maxHarga,
          onApply: (search, tipe, minHarga, maxHarga) {
            provider.applyFilters(
              search: search,
              tipe: tipe,
              minHarga: minHarga,
              maxHarga: maxHarga,
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
        automaticallyImplyLeading: false,
        title: Row(
          children: [
            const Icon(Icons.home_work_rounded, color: Colors.white),
            const SizedBox(width: 8),
            Text(
              "My Kos",
              style: GoogleFonts.inter(
                color: Colors.white,
                fontWeight: FontWeight.bold,
                fontSize: 20,
              ),
            ),
          ],
        ),
        actions: [
          PopupMenuButton<String>(
            icon: const Icon(Icons.more_vert, color: Colors.white),
            onSelected: (value) {
              if (value == 'pusat_bantuan') {
                _openWhatsApp();
              } else if (value == 'kebijakan_privasi') {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const PrivacyScreen()),
                );
              }
            },
            itemBuilder: (BuildContext context) => [
              const PopupMenuItem(value: 'pusat_bantuan', child: Text('Pusat Bantuan')),
              const PopupMenuItem(value: 'kebijakan_privasi', child: Text('Kebijakan Privasi')),
            ],
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        child: Column(
          children: [
            const SizedBox(height: 16),
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
                        hintText: "Cari lokasi, kota, atau nama kos...",
                        hintStyle: GoogleFonts.inter(color: Colors.grey, fontSize: 14),
                        prefixIcon: const Icon(Icons.search, color: Colors.grey, size: 20),
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
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
                    icon: const Icon(Icons.tune, color: Colors.white, size: 20),
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
                  
                  if (provider.isLoading) {
                    return const Center(
                      child: CircularProgressIndicator(color: Color(0xFF0D3B66)),
                    );
                  }
                  
                  if (kosList.isEmpty) {
                    return Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.search_off_rounded, size: 72, color: Colors.grey[350]),
                          const SizedBox(height: 16),
                          Text(
                            'Kos tidak ditemukan',
                            style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w600, color: Colors.grey[500]),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'Coba ubah kata kunci atau filter pencarian',
                            style: GoogleFonts.inter(fontSize: 13, color: Colors.grey[400]),
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
                      return KosCard(kos: kosList[index]);
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