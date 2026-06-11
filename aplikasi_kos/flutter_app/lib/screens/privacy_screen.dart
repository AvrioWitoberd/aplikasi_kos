import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/privacy_provider.dart';
import '../models/privacy_model.dart';

class PrivacyScreen extends StatefulWidget {
  const PrivacyScreen({Key? key}) : super(key: key);

  @override
  State<PrivacyScreen> createState() => _PrivacyScreenState();
}

class _PrivacyScreenState extends State<PrivacyScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PrivacyProvider>().loadPrivacy();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text(
          'Kebijakan Privasi',
          style: GoogleFonts.inter(
            fontWeight: FontWeight.w600,
            color: Colors.white,  // TAMBAHKAN INI - WARNA PUTIH
          ),
        ),
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white), // ICON BACK JUGA PUTIH
      ),
      body: Consumer<PrivacyProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(
              child: CircularProgressIndicator(color: Color(0xFF0D3B66)),
            );
          }
          
          if (provider.hasError) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                  const SizedBox(height: 16),
                  Text(
                    'Gagal memuat data',
                    style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    provider.errorMessage,
                    style: GoogleFonts.inter(fontSize: 12, color: Colors.grey[600]),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => provider.loadPrivacy(),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0D3B66),
                    ),
                    child: Text('Coba Lagi', style: GoogleFonts.inter(color: Colors.white)),
                  ),
                ],
              ),
            );
          }
          
          final data = provider.privacyData;
          
          if (data == null) {
            return const Center(
              child: Text('Tidak ada data'),
            );
          }
          
          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (data.introText.isNotEmpty)
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.blue[50],
                      borderRadius: BorderRadius.circular(12),
                      border: Border(left: BorderSide(color: Colors.blue[400]!, width: 4)),
                    ),
                    child: Text(
                      _parseHtmlContent(data.introText),
                      style: GoogleFonts.inter(
                        fontSize: 14,
                        color: Colors.blue[800],
                        height: 1.6,
                      ),
                    ),
                  ),
                
                const SizedBox(height: 24),
                
                if (data.sections.isNotEmpty)
                  ...data.sections.map((section) => _buildSection(section)),
                
                if (data.sections.isEmpty)
                  Center(
                    child: Text(
                      'Belum ada kebijakan privasi.',
                      style: GoogleFonts.inter(color: Colors.grey[500]),
                    ),
                  ),
                
                const SizedBox(height: 16),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildSection(PrivacySection section) {
    return Container(
      margin: const EdgeInsets.only(bottom: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            section.judul,
            style: GoogleFonts.inter(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: const Color(0xFF1F2937),
            ),
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.05),
                  spreadRadius: 1,
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Text(
              _parseHtmlContent(section.konten),
              style: GoogleFonts.inter(
                fontSize: 14,
                height: 1.6,
                color: Colors.grey[700],
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _parseHtmlContent(String html) {
    String text = html;
    
    text = text.replaceAll('<p>', '');
    text = text.replaceAll('</p>', '\n\n');
    text = text.replaceAll('<br>', '\n');
    text = text.replaceAll('<br/>', '\n');
    text = text.replaceAll('<br />', '\n');
    text = text.replaceAll('<ul>', '');
    text = text.replaceAll('</ul>', '');
    text = text.replaceAll('<li>', '• ');
    text = text.replaceAll('</li>', '\n');
    text = text.replaceAll('<strong>', '');
    text = text.replaceAll('</strong>', '');
    text = text.replaceAll('<b>', '');
    text = text.replaceAll('</b>', '');
    text = text.replaceAll('<em>', '');
    text = text.replaceAll('</em>', '');
    text = text.replaceAll('<i>', '');
    text = text.replaceAll('</i>', '');
    text = text.replaceAll('<h5>', '\n');
    text = text.replaceAll('</h5>', '\n');
    text = text.replaceAll('<div>', '');
    text = text.replaceAll('</div>', '\n');
    text = text.replaceAll('<ol>', '');
    text = text.replaceAll('</ol>', '');
    
    text = text.replaceAll(RegExp(r'<[^>]*>'), '');
    text = text.replaceAll(RegExp(r'\n{3,}'), '\n\n');
    
    return text.trim();
  }
}