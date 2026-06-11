import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:share_plus/share_plus.dart';
import 'package:http/http.dart' as http;
import '../providers/detail_kos_provider.dart';
import '../providers/auth_provider.dart';
import '../screens/profile_screen.dart';
import '../providers/kos_provider.dart';
import '../utils/url_helper.dart';

class DetailKosScreen extends StatefulWidget {
  final int idKos;

  const DetailKosScreen({Key? key, required this.idKos}) : super(key: key);

  @override
  State<DetailKosScreen> createState() => _DetailKosScreenState();
}

class _DetailKosScreenState extends State<DetailKosScreen> {
  int _currentImageIndex = 0;
  int _selectedRating = 0;
  bool _hasRated = false;
  bool _isRatingLoading = false;
  String? _favoritAction;
  late PageController _pageController;

// Geocoding: Ubah alamat menjadi koordinat (lat, lng)
  Future<Map<String, double>?> _geocodeAddress(String address) async {
    try {
      final encodedAddress = Uri.encodeComponent(address);
      final url =
          'https://nominatim.openstreetmap.org/search?q=$encodedAddress&format=json&limit=1';

      final response = await http.get(Uri.parse(url));

      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        if (data.isNotEmpty) {
          return {
            'lat': double.parse(data[0]['lat']),
            'lng': double.parse(data[0]['lon']),
          };
        }
      }
      return null;
    } catch (e) {
      print('Geocoding error: $e');
      return null;
    }
  }

// Fungsi untuk mendapatkan koordinat dari shortlink Google Maps atau alamat
  Future<Map<String, double>?> _getCoordinatesFromShortlink(
      String shortUrl, String address) async {
    try {
      // Coba dari shortlink dulu
      if (shortUrl.isNotEmpty) {
        final response = await http.head(Uri.parse(shortUrl));
        final location = response.headers['location'];
        if (location != null) {
          final coords = _extractCoordinatesFromLongUrl(location);
          if (coords != null) return coords;
        }
      }

      // Jika gagal, coba ekstrak dari URL langsung
      final directCoords = _extractCoordinatesFromLongUrl(shortUrl);
      if (directCoords != null) return directCoords;

      // Terakhir, coba geocoding dari alamat
      if (address.isNotEmpty) {
        print('🔄 Mencoba geocoding alamat: $address');
        return await _geocodeAddress(address);
      }

      return null;
    } catch (e) {
      print('Error getting coordinates: $e');

      // Fallback: coba geocoding dari alamat
      if (address.isNotEmpty) {
        return await _geocodeAddress(address);
      }
      return null;
    }
  }

  // Ekstrak koordinat dari URL panjang Google Maps
  Map<String, double>? _extractCoordinatesFromLongUrl(String url) {
    // Format: @-7.123456,112.123456
    final regex = RegExp(r'@(-?\d+\.\d+),(-?\d+\.\d+)');
    final match = regex.firstMatch(url);
    if (match != null) {
      return {
        'lat': double.parse(match.group(1)!),
        'lng': double.parse(match.group(2)!),
      };
    }

    // Format: !2d112.123456!3d-7.123456
    final regex2 = RegExp(r'!2d([0-9\.]+)!3d(-?[0-9\.]+)');
    final match2 = regex2.firstMatch(url);
    if (match2 != null) {
      return {
        'lng': double.parse(match2.group(1)!),
        'lat': double.parse(match2.group(2)!),
      };
    }

    return null;
  }

  void _showSuccessSnackbar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.green,
        duration: const Duration(seconds: 2),
      ),
    );
  }

  void _showErrorSnackbar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red,
        duration: const Duration(seconds: 2),
      ),
    );
  }

  @override
  void initState() {
    super.initState();
    _pageController = PageController(); // <== TAMBAHKAN INI

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await context.read<DetailKosProvider>().loadDetail(widget.idKos);

      if (_isLoggedIn(context)) {
        await _cekUserRating(widget.idKos, context);
      }
    });
  }

  // Cek apakah user sudah login
  bool _isLoggedIn(BuildContext context) {
    // Gunakan Provider.of dengan listen: false agar tidak rebuild
    final auth = Provider.of<AuthProvider>(context, listen: false);
    return auth.isLoggedIn;
  }

  int? _getCurrentUserId(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context, listen: false);
    return auth.userId;
  }

  // Redirect ke halaman profile jika belum login
  void _redirectToLogin(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) =>
            const ProfileScreen(showBackButton: true), // HAPUS redirectFrom
      ),
    );
  }

  Future<void> _refreshDetailKos() async {
    await context.read<DetailKosProvider>().loadDetail(widget.idKos);

    if (_isLoggedIn(context)) {
      await _cekUserRating(widget.idKos, context);
    }
  }

  Future<void> _openWhatsApp(
      String? phoneNumber, String namaKos, BuildContext context) async {
    // Cek login dulu
    if (!_isLoggedIn(context)) {
      _redirectToLogin(context);
      return;
    }

    if (phoneNumber == null || phoneNumber.isEmpty) {
      _showSnackbar('Nomor WhatsApp tidak tersedia');
      return;
    }

    String cleanNumber = phoneNumber.replaceAll(RegExp(r'[^0-9]'), '');

    if (cleanNumber.isEmpty) {
      _showSnackbar('Nomor WhatsApp tidak valid');
      return;
    }

    if (cleanNumber.startsWith('0')) {
      cleanNumber = '62${cleanNumber.substring(1)}';
    } else if (!cleanNumber.startsWith('62')) {
      cleanNumber = '62$cleanNumber';
    }

    final url =
        'https://wa.me/$cleanNumber?text=Halo, saya tertarik dengan kos $namaKos';

    if (await canLaunchUrl(Uri.parse(url))) {
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
    } else {
      _showSnackbar('Tidak dapat membuka WhatsApp');
    }
  }

  void _openMaps(String? url) async {
    if (url == null || url.isEmpty) {
      _showSnackbar('Link tidak tersedia');
      return;
    }

    if (await canLaunchUrl(Uri.parse(url))) {
      await launchUrl(
        Uri.parse(url),
        mode: LaunchMode.externalApplication,
      );
    } else {
      _showSnackbar('Tidak dapat membuka maps');
    }
  }

  void _shareKos(String namaKos, int idKos) {
    final String url = 'https://mykos.com/detail_kos.php?id=$idKos';
    final String text = 'Lihat kos $namaKos di MyKos!\n\n$url';
    Share.share(text, subject: namaKos);
  }

  void _showSnackbar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  String _formatHarga(int harga) {
    return harga.toString().replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (Match m) => '${m[1]}.',
        );
  }

// ========== RATING FUNCTIONS ==========

  Future<void> _cekUserRating(int idKos, BuildContext context) async {
    // Cek login langsung dari AuthProvider
    final auth = Provider.of<AuthProvider>(context, listen: false);

    if (!auth.isLoggedIn) {
      debugPrint('❌ User not logged in, skip rating check');
      return;
    }

    final userId = auth.userId;
    if (userId == null) {
      debugPrint('❌ User ID is null, skip rating check');
      return;
    }

    debugPrint('✅ Checking user rating for user ID: $userId');

    try {
      final url = Uri.parse(
          '${UrlHelper.getBaseUrl()}/rating/cek_rating.php?id=$idKos&id_user=$userId');
      final response = await http.get(url);

      debugPrint('📡 Response Status: ${response.statusCode}');
      debugPrint('📡 Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);
        if (data['status'] == 'success' && data['user_rating'] != null) {
          setState(() {
            _hasRated = true;
            _selectedRating = data['user_rating'] as int;
            debugPrint('✅ User already rated: $_selectedRating stars');
          });
        }
      }
    } catch (e) {
      debugPrint('❌ Error cek rating: $e');
    }
  }

  Future<void> _kirimRating(int idKos, int skor, BuildContext context) async {
    debugPrint('📝 Kirim rating - isLoggedIn: ${_isLoggedIn(context)}');

    // cek login
    if (!_isLoggedIn(context)) {
      _redirectToLogin(context);
      return;
    }

    final auth = context.read<AuthProvider>();

    if (auth.userId == null) {
      _redirectToLogin(context);
      return;
    }

    // sudah rating
    if (_hasRated) {
      _showSnackbar('Anda sudah memberikan rating sebelumnya!');
      return;
    }

    // belum pilih bintang
    if (skor == 0) {
      _showSnackbar('Silakan pilih rating terlebih dahulu!');
      return;
    }

    try {
      final url = Uri.parse(
        '${UrlHelper.getBaseUrl()}/rating/tambah_rating.php',
      );

      final request = http.MultipartRequest('POST', url);

      request.fields['id_user'] = auth.userId.toString();
      request.fields['role'] = auth.role ?? '';
      request.fields['id_kos'] = idKos.toString();
      request.fields['skor'] = skor.toString();

      final response = await request.send();

      final responseBody = await response.stream.bytesToString();

      final data = json.decode(responseBody);

      debugPrint('📡 Response Rating: $responseBody');

      if (data['status'] == 'success') {
        setState(() {
          _hasRated = true;
          _selectedRating = skor;
        });
        _showRatingThankYouModal();

        Future.microtask(() async {
          await _refreshDetailKos();

          // refresh home list juga
          if (mounted) {
            await context.read<KosProvider>().loadKos();
          }
        });
      } else if (data['status'] == 'forbidden') {
        _showSnackbar(data['message']);
      } else if (data['status'] == 'login_required') {
        _redirectToLogin(context);
      } else {
        _showSnackbar(data['message'] ?? 'Gagal mengirim rating');
      }
    } catch (e) {
      debugPrint('❌ Error rating: $e');
      _showSnackbar('Gagal mengirim rating');
    }
  }

  void _showRatingThankYouModal() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.check_circle, size: 60, color: Colors.green),
            const SizedBox(height: 16),
            Text(
              'Terima Kasih!',
              style:
                  GoogleFonts.inter(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Text(
              'Terima kasih telah memberikan rating pada kos ini.',
              style: GoogleFonts.inter(fontSize: 13, color: Colors.grey[600]),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF0D3B66),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(30),
                ),
              ),
              child: Text(
                'Tutup',
                style: GoogleFonts.inter(color: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _buildStaticMapUrl(double lat, double lng) {
    const String apiKey = 'AIzaSyB_inw9ZzUNGRfJ9xdAzil20mIw9BZ2VkE';
    return 'https://maps.googleapis.com/maps/api/staticmap?center=$lat,$lng&zoom=15&size=400x220&markers=color:red%7C$lat,$lng&key=$apiKey';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text(
          'Detail Kos',
          style: GoogleFonts.inter(
              fontWeight: FontWeight.w600, color: Colors.white),
        ),
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            if (_favoritAction != null) {
              Navigator.pop(context, _favoritAction);
            } else {
              Navigator.pop(context);
            }
          },
        ),
        actions: [
          // Tombol Share
          IconButton(
            icon: const Icon(Icons.share, color: Colors.white),
            onPressed: () {
              final kos = context.read<DetailKosProvider>().data?.kos;
              if (kos != null) {
                _shareKos(kos.namaKos, kos.idKos);
              }
            },
          ),

// Tombol Save (Favorit) - MODIFIKASI
          Consumer<DetailKosProvider>(
            builder: (context, provider, child) {
              return IconButton(
                icon: Icon(
                  provider.isFavorit ? Icons.bookmark : Icons.bookmark_border,
                  color: provider.isFavorit ? Colors.yellow : Colors.white,
                ),
                onPressed: provider.isFavoritLoading
                    ? null
                    : () async {
                        if (!_isLoggedIn(context)) {
                          _redirectToLogin(context);
                        } else {
                          final auth = context.read<AuthProvider>();
                          if (auth.userId != null) {
                            final success = await provider.toggleFavorit(
                                widget.idKos, auth.userId!);
                            if (success) {
                              if (provider.isFavorit) {
                                _showSuccessSnackbar('Ditambahkan ke favorit');
                                _favoritAction = 'added';
                              } else {
                                _showErrorSnackbar('Dihapus dari favorit');
                                _favoritAction = 'removed';
                              }
                            } else {
                              _showErrorSnackbar('Gagal menyimpan');
                            }
                          }
                        }
                      },
              );
            },
          ),
        ],
      ),
      body: Consumer<DetailKosProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(
              child: CircularProgressIndicator(color: Color(0xFF0D3B66)),
            );
          }

          if (provider.hasError || provider.data == null) {
            return Center(
              child: // Star Rating - Ganti bagian onTap
                  Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(5, (index) {
                  final starValue = index + 1;
                  return GestureDetector(
                    onTap: _hasRated
                        ? null
                        : () {
                            // Cek login langsung dari Provider
                            final auth = Provider.of<AuthProvider>(context,
                                listen: false);
                            if (!auth.isLoggedIn) {
                              _redirectToLogin(context);
                            } else {
                              setState(() {
                                _selectedRating = starValue;
                              });
                            }
                          },
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                      child: Icon(
                        starValue <= _selectedRating
                            ? Icons.star
                            : Icons.star_border,
                        size: 40,
                        color: starValue <= _selectedRating
                            ? Colors.amber
                            : Colors.grey[400],
                      ),
                    ),
                  );
                }),
              ),
            );
          }

          final data = provider.data!;
          final kos = data.kos;
          final fotoList = data.fotoList;
          final hasMultipleImages = fotoList.length > 1;

          return SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Carousel Foto
                Stack(
                  children: [
                    // Carousel Foto - VERSION YANG BISA DI SWIPE & DIKLIK
                    SizedBox(
                      height: 300,
                      child: fotoList.isEmpty
                          ? Container(
                              color: Colors.grey[200],
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.image_not_supported,
                                      size: 64, color: Colors.grey[400]),
                                  const SizedBox(height: 8),
                                  Text(
                                    'Tidak ada foto',
                                    style: GoogleFonts.inter(
                                        color: Colors.grey[500]),
                                  ),
                                ],
                              ),
                            )
                          : Stack(
                              children: [
                                // CAROUSEL UTAMA
                                PageView.builder(
                                  controller: _pageController,
                                  itemCount: fotoList.length,
                                  onPageChanged: (index) {
                                    setState(() {
                                      _currentImageIndex = index;
                                    });
                                  },
                                  itemBuilder: (context, index) {
                                    return CachedNetworkImage(
                                      imageUrl:
                                          UrlHelper.getImageUrl(fotoList[index], 'foto_kos'),
                                      fit: BoxFit.cover,
                                      width: double.infinity,
                                      placeholder: (context, url) => Container(
                                        color: Colors.grey[200],
                                        child: const Center(
                                            child: CircularProgressIndicator()),
                                      ),
                                      errorWidget: (context, url, error) =>
                                          Container(
                                        color: Colors.grey[200],
                                        child: Column(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.broken_image,
                                                size: 50,
                                                color: Colors.grey[400]),
                                            Text('Gagal memuat gambar',
                                                style: GoogleFonts.inter(
                                                    fontSize: 12,
                                                    color: Colors.grey[500])),
                                          ],
                                        ),
                                      ),
                                    );
                                  },
                                ),
                                // INDICATOR DOTS YANG BISA DIKLIK
                                if (fotoList.length > 1)
                                  Positioned(
                                    bottom: 16,
                                    left: 0,
                                    right: 0,
                                    child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: List.generate(
                                        fotoList.length,
                                        (index) => GestureDetector(
                                          onTap: () {
                                            // PINDAH FOTO SAAT TITIK DIKLIK
                                            _pageController.animateToPage(
                                              index,
                                              duration: const Duration(
                                                  milliseconds: 300),
                                              curve: Curves.easeInOut,
                                            );
                                          },
                                          child: AnimatedContainer(
                                            duration: const Duration(
                                                milliseconds: 200),
                                            margin: const EdgeInsets.symmetric(
                                                horizontal: 4),
                                            width: _currentImageIndex == index
                                                ? 24
                                                : 8,
                                            height: 8,
                                            decoration: BoxDecoration(
                                              color: _currentImageIndex == index
                                                  ? const Color(0xFF0D3B66)
                                                  : Colors.white
                                                      .withOpacity(0.6),
                                              borderRadius:
                                                  BorderRadius.circular(4),
                                            ),
                                          ),
                                        ),
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                    ),
                  ],
                ),

                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Nama Kos
                      Text(
                        kos.namaKos,
                        style: GoogleFonts.inter(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF1F2937)),
                      ),
                      const SizedBox(height: 4),

                      // Alamat
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(top: 2),
                            child: Icon(Icons.location_on,
                                size: 14, color: Colors.red[400]),
                          ),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              kos.alamatLengkap ?? kos.kota,
                              style: GoogleFonts.inter(
                                  fontSize: 12, color: Colors.grey[600]),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),

                      // Badges
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: [
                          _buildBadge(Icons.people_outline,
                              'Kos ${kos.tipeKos}', Colors.blue),
                          _buildBadge(
                              Icons.star,
                              '${kos.rataRating.toStringAsFixed(1)} (${kos.totalUlasan} ulasan)',
                              Colors.amber),
                          _buildBadge(Icons.door_front_door_outlined,
                              '${kos.jumlahKamar} tersedia', Colors.green),
                        ],
                      ),
                      const SizedBox(height: 16),

                      const Divider(),
                      const SizedBox(height: 16),

                      // Deskripsi
                      Text(
                        'Deskripsi Kos',
                        style: GoogleFonts.inter(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF1F2937)),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        (kos.deskripsi != null && kos.deskripsi!.isNotEmpty)
                            ? kos.deskripsi!
                            : 'Tidak ada deskripsi.',
                        style: GoogleFonts.inter(
                            fontSize: 13, color: Colors.grey[600], height: 1.5),
                      ),
                      const SizedBox(height: 16),

                      // Fasilitas
                      if (data.fasilitasList.isNotEmpty &&
                          data.fasilitasList.first.isNotEmpty)
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Fasilitas Kos',
                              style: GoogleFonts.inter(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: const Color(0xFF1F2937)),
                            ),
                            const SizedBox(height: 8),
                            Wrap(
                              spacing: 8,
                              runSpacing: 8,
                              children: data.fasilitasList
                                  .where((f) => f.trim().isNotEmpty)
                                  .map((f) => Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 12, vertical: 6),
                                        decoration: BoxDecoration(
                                          color: Colors.grey[100],
                                          borderRadius:
                                              BorderRadius.circular(20),
                                        ),
                                        child: Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            Icon(Icons.check_circle,
                                                size: 14,
                                                color: Colors.green[400]),
                                            const SizedBox(width: 4),
                                            Text(
                                              f.trim(),
                                              style: GoogleFonts.inter(
                                                  fontSize: 12,
                                                  color: Colors.grey[700]),
                                            ),
                                          ],
                                        ),
                                      ))
                                  .toList(),
                            ),
                            const SizedBox(height: 16),
                          ],
                        ),

                      // Peraturan Kos
                      Text(
                        'Peraturan Kos',
                        style: GoogleFonts.inter(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF1F2937)),
                      ),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.grey[100],
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          (kos.peraturanKos != null &&
                                  kos.peraturanKos!.isNotEmpty)
                              ? kos.peraturanKos!
                              : 'Belum ada peraturan yang ditambahkan.',
                          style: GoogleFonts.inter(
                              fontSize: 13,
                              color: Colors.grey[600],
                              height: 1.5),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Area Sekitar Kos
                      Text(
                        'Area Sekitar Kos',
                        style: GoogleFonts.inter(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF1F2937)),
                      ),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.grey[100],
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          (kos.areaSekitarKos != null &&
                                  kos.areaSekitarKos!.isNotEmpty)
                              ? kos.areaSekitarKos!
                              : 'Belum ada informasi area sekitar.',
                          style: GoogleFonts.inter(
                              fontSize: 13,
                              color: Colors.grey[600],
                              height: 1.5),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // ========== LOKASI MAPS ==========
                      Text(
                        'Lokasi di Maps',
                        style: GoogleFonts.inter(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF1F2937)),
                      ),
                      const SizedBox(height: 8),

                      // Alamat lengkap
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.grey[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.grey[200]!),
                        ),
                        child: Row(
                          children: [
                            Icon(Icons.location_on,
                                size: 16, color: Colors.red[400]),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                kos.alamatLengkap ?? kos.kota,
                                style: GoogleFonts.inter(
                                    fontSize: 13, color: Colors.grey[700]),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),

// Preview Peta Statis dengan Google Maps API
                      if (kos.alamatLengkap != null &&
                          kos.alamatLengkap!.isNotEmpty)
                        FutureBuilder<Map<String, double>?>(
                          future: _getCoordinatesFromShortlink(
                            data.directMapUrl ?? data.embedMapUrl ?? '',
                            kos.alamatLengkap!,
                          ),
                          builder: (context, snapshot) {
                            if (snapshot.connectionState ==
                                ConnectionState.waiting) {
                              return Container(
                                height: 200,
                                width: double.infinity,
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(16),
                                  color: Colors.grey[200],
                                ),
                                child: const Center(
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      CircularProgressIndicator(
                                          color: Color(0xFF0D3B66)),
                                      SizedBox(height: 12),
                                      Text('Memuat peta...'),
                                    ],
                                  ),
                                ),
                              );
                            }

                            final coords = snapshot.data;

                            if (coords != null) {
                              final imageUrl = _buildStaticMapUrl(
                                  coords['lat']!, coords['lng']!);
                              return GestureDetector(
                                onTap: () => _openMaps(
                                    data.directMapUrl ?? data.embedMapUrl),
                                child: Container(
                                  height: 220,
                                  width: double.infinity,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(16),
                                    boxShadow: [
                                      BoxShadow(
                                        color: Colors.black.withOpacity(0.1),
                                        spreadRadius: 1,
                                        blurRadius: 8,
                                        offset: const Offset(0, 2),
                                      ),
                                    ],
                                  ),
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(16),
                                    child: Stack(
                                      children: [
                                        Image.network(
                                          imageUrl,
                                          width: double.infinity,
                                          height: 220,
                                          fit: BoxFit.cover,
                                          loadingBuilder: (context, child,
                                              loadingProgress) {
                                            if (loadingProgress == null)
                                              return child;
                                            return Container(
                                              color: Colors.grey[200],
                                              child: const Center(
                                                  child:
                                                      CircularProgressIndicator()),
                                            );
                                          },
                                          errorBuilder:
                                              (context, error, stackTrace) {
                                            print('❌ Image error: $error');
                                            return Container(
                                              color: Colors.grey[200],
                                              child: const Center(
                                                child: Icon(Icons.broken_image,
                                                    size: 50,
                                                    color: Colors.grey),
                                              ),
                                            );
                                          },
                                        ),
                                        Positioned(
                                          bottom: 8,
                                          right: 8,
                                          child: Container(
                                            padding: const EdgeInsets.symmetric(
                                                horizontal: 10, vertical: 5),
                                            decoration: BoxDecoration(
                                              color: Colors.black54,
                                              borderRadius:
                                                  BorderRadius.circular(20),
                                            ),
                                            child: const Text(
                                              'Google Maps',
                                              style: TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 10),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              );
                            } else {
                              // Fallback: tombol buka peta
                              return GestureDetector(
                                onTap: () => _openMaps(
                                    data.directMapUrl ?? data.embedMapUrl),
                                child: Container(
                                  height: 180,
                                  width: double.infinity,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(16),
                                    color: const Color(0xFF0D3B66)
                                        .withOpacity(0.05),
                                    border:
                                        Border.all(color: Colors.grey[200]!),
                                  ),
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.map,
                                          size: 48,
                                          color: const Color(0xFF0D3B66)),
                                      const SizedBox(height: 12),
                                      Text(
                                        'Klik untuk buka peta',
                                        style: GoogleFonts.inter(
                                            fontSize: 14,
                                            fontWeight: FontWeight.w500),
                                      ),
                                      const SizedBox(height: 8),
                                      Padding(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 32),
                                        child: Text(
                                          kos.alamatLengkap!,
                                          style: GoogleFonts.inter(
                                              fontSize: 12,
                                              color: Colors.grey[600]),
                                          textAlign: TextAlign.center,
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            }
                          },
                        )
                      else
                        Container(
                          height: 180,
                          width: double.infinity,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(16),
                            color: Colors.grey[100],
                          ),
                          child: const Center(
                            child: Text("Lokasi tidak tersedia"),
                          ),
                        ),
                      const SizedBox(height: 12),

                      // Tombol Buka di Google Maps (tetap dipertahankan)
                      if (data.directMapUrl != null &&
                          data.directMapUrl!.isNotEmpty &&
                          data.directMapUrl != '#')
                        Center(
                          child: ElevatedButton.icon(
                            onPressed: () => _openMaps(data.directMapUrl),
                            icon: const Icon(Icons.map, size: 18),
                            label: Text(
                              'Buka di Google Maps',
                              style: GoogleFonts.inter(
                                  fontSize: 14, fontWeight: FontWeight.w600),
                            ),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.red,
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 24, vertical: 12),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(30),
                              ),
                              elevation: 0,
                            ),
                          ),
                        ),

                      const SizedBox(height: 24),

                      // ========== RATING SECTION ==========
                      const Divider(),
                      const SizedBox(height: 16),

                      Text(
                        'Berikan Rating untuk Kos Ini',
                        style: GoogleFonts.inter(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF1F2937)),
                      ),
                      const SizedBox(height: 16),

                      // Star Rating
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: List.generate(5, (index) {
                          final starValue = index + 1;
                          return GestureDetector(
                            onTap: _hasRated
                                ? null
                                : () {
                                    // Cek login dulu
                                    if (!_isLoggedIn(context)) {
                                      _redirectToLogin(context);
                                    } else {
                                      setState(() {
                                        _selectedRating = starValue;
                                      });
                                    }
                                  },
                            child: Padding(
                              padding:
                                  const EdgeInsets.symmetric(horizontal: 8),
                              child: Icon(
                                starValue <= _selectedRating
                                    ? Icons.star
                                    : Icons.star_border,
                                size: 40,
                                color: starValue <= _selectedRating
                                    ? Colors.amber
                                    : Colors.grey[400],
                              ),
                            ),
                          );
                        }),
                      ),
                      const SizedBox(height: 16),

                      // Rating Status
                      if (_hasRated && _isLoggedIn(context))
                        Container(
                          padding: const EdgeInsets.symmetric(
                              vertical: 8, horizontal: 16),
                          decoration: BoxDecoration(
                            color: Colors.green[50],
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.check_circle,
                                  size: 16, color: Colors.green[600]),
                              const SizedBox(width: 8),
                              Text(
                                'Terima kasih atas rating yang Anda berikan!',
                                style: GoogleFonts.inter(
                                    fontSize: 12, color: Colors.green[700]),
                              ),
                            ],
                          ),
                        ),

                      if (!_hasRated && _isLoggedIn(context))
                        Center(
                          child: ElevatedButton(
                            onPressed: () => _kirimRating(
                                kos.idKos, _selectedRating, context),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF0D3B66),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 32, vertical: 12),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(30),
                              ),
                            ),
                            child: Text(
                              'Kirim Rating',
                              style: GoogleFonts.inter(
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                        ),

                      if (!_isLoggedIn(context))
                        Center(
                          child: ElevatedButton(
                            onPressed: () => _redirectToLogin(context),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.grey,
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 32, vertical: 12),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(30),
                              ),
                            ),
                            child: Text(
                              'Login untuk memberi rating',
                              style: GoogleFonts.inter(
                                  fontWeight: FontWeight.w600),
                            ),
                          ),
                        ),

                      const SizedBox(height: 24),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
      bottomNavigationBar: Consumer<DetailKosProvider>(
        builder: (context, provider, child) {
          if (provider.data == null) return const SizedBox.shrink();

          final kos = provider.data!.kos;
          // PRIORITAS: noHpKos (dari tabel kos), fallback: noHpPemilik (dari tabel users)
          final waNumber = kos.noHpKos ?? kos.noHpPemilik ?? '';

          print('DEBUG: noHpKos: "${kos.noHpKos}"');
          print('DEBUG: noHpPemilik: "${kos.noHpPemilik}"');
          print('DEBUG: Final WA Number: "$waNumber"');

          return Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.1),
                  spreadRadius: 1,
                  blurRadius: 10,
                  offset: const Offset(0, -3),
                ),
              ],
            ),
            child: SafeArea(
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          'Mulai dari',
                          style: GoogleFonts.inter(
                              fontSize: 11, color: Colors.grey[500]),
                        ),
                        Text(
                          'Rp ${_formatHarga(kos.hargaPerBulan)}',
                          style: GoogleFonts.inter(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF0D3B66),
                          ),
                        ),
                        Text(
                          '/bulan',
                          style: GoogleFonts.inter(
                              fontSize: 11, color: Colors.grey[500]),
                        ),
                      ],
                    ),
                  ),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () =>
                          _openWhatsApp(waNumber, kos.namaKos, context),
                      icon: const Icon(Icons.message, size: 20),
                      label: Text(
                        'Hubungi Pemilik',
                        style: GoogleFonts.inter(fontWeight: FontWeight.w600),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF25D366),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildBadge(IconData icon, String label, Color iconColor) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: Colors.grey[100],
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: iconColor),
          const SizedBox(width: 4),
          Text(
            label,
            style: GoogleFonts.inter(fontSize: 11, color: Colors.grey[700]),
          ),
        ],
      ),
    );
  }
}
