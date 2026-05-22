import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/auth_provider.dart';
import '../auth/role_selection_screen.dart';
import '../main_navigation.dart';
import '../home/privacy_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  void _showLogoutConfirmation(BuildContext context) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: Text(
            "Konfirmasi Logout",
            style: GoogleFonts.inter(fontWeight: FontWeight.bold),
          ),
          content: Text(
            "Apakah anda yakin ingin keluar dari akun?",
            style: GoogleFonts.inter(color: Colors.grey[700]),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: Text(
                "Batal",
                style: GoogleFonts.inter(
                  color: Colors.grey[600],
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
                _handleLogout(context);
              },
              child: Text(
                "Keluar",
                style: GoogleFonts.inter(
                  color: Colors.red,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ],
        );
      },
    );
  }

  void _handleLogout(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    authProvider.logout();
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(
        builder: (_) => const MainNavigationScreen(initialIndex: 0),
      ),
      (route) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = context.watch<AuthProvider>();
    
    if (!authProvider.isLoggedIn) {
      return const RoleSelectionScreen();
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FA),
      appBar: AppBar(
        title: Text(
          "Profil Saya",
          style: GoogleFonts.inter(
            color: Colors.black87,
            fontWeight: FontWeight.bold,
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Header card
            Container(
              width: double.infinity,
              color: Colors.white,
              padding: const EdgeInsets.fromLTRB(16, 28, 16, 28),
              child: Column(
                children: [
                  CircleAvatar(
                    radius: 48,
                    backgroundColor: const Color(0xFF0D3B66).withAlpha(25),
                    child: const Icon(
                      Icons.person_rounded,
                      size: 52,
                      color: Color(0xFF0D3B66),
                    ),
                  ),
                  const SizedBox(height: 14),
                  Text(
                    "User Dummy",
                    style: GoogleFonts.inter(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    "user@gmail.com",
                    style: GoogleFonts.inter(
                      fontSize: 14,
                      color: Colors.grey[500],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            // Menu section
            Container(
              margin: const EdgeInsets.symmetric(horizontal: 16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withAlpha(10),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                children: [
                  _buildMenuItem(
                    icon: Icons.help_outline_rounded,
                    title: "Pusat Bantuan",
                    onTap: () async {
                      final Uri url = Uri.parse(
                          'https://wa.me/6285784649183?text=${Uri.encodeComponent("Halo admin, saya butuh bantuan terkait aplikasi My Kos")}');
                      launchUrl(url, mode: LaunchMode.externalApplication);
                    },
                  ),
                  const Divider(height: 1, indent: 56, endIndent: 16),
                  _buildMenuItem(
                    icon: Icons.privacy_tip_outlined,
                    title: "Kebijakan Privasi",
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => const PrivacyScreen()),
                      );
                    },
                  ),
                  const Divider(height: 1, indent: 56, endIndent: 16),
                  if (authProvider.isLoggedIn)
                    _buildMenuItem(
                      icon: Icons.logout_rounded,
                      title: "Logout",
                      textColor: Colors.red,
                      iconColor: Colors.red,
                      showChevron: false,
                      onTap: () => _showLogoutConfirmation(context),
                    )
                  else
                    _buildMenuItem(
                      icon: Icons.login_rounded,
                      title: "Login",
                      textColor: const Color(0xFF0D3B66),
                      iconColor: const Color(0xFF0D3B66),
                      showChevron: true,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (_) => const RoleSelectionScreen()),
                        );
                      },
                    ),
                ],
              ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuItem({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
    Color? textColor,
    Color? iconColor,
    bool showChevron = true,
  }) {
    return ListTile(
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
      leading: Container(
        width: 36,
        height: 36,
        decoration: BoxDecoration(
          color: (iconColor ?? const Color(0xFF0D3B66)).withAlpha(18),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Icon(icon, color: iconColor ?? const Color(0xFF0D3B66), size: 20),
      ),
      title: Text(
        title,
        style: GoogleFonts.inter(
          fontSize: 15,
          fontWeight: FontWeight.w500,
          color: textColor ?? Colors.black87,
        ),
      ),
      trailing: showChevron
          ? Icon(Icons.chevron_right_rounded, color: Colors.grey[400])
          : null,
      onTap: onTap,
    );
  }
}
