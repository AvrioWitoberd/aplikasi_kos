import 'package:flutter/material.dart';
import '../main_navigation.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _navigateToNext();
  }

  Future<void> _navigateToNext() async {
    await Future.delayed(const Duration(seconds: 2));
    if (!mounted) return;
    // Browse-first: always go to Home. Login is only required for
    // specific guarded actions (favorite, rating, booking).
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => const MainNavigationScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Image.asset(
              'assets/images/logo.jpeg',
              width: 150,
              height: 150,
              errorBuilder: (context, error, stackTrace) {
                // Fallback icon if image fails to load
                return const Icon(
                  Icons.home_work_rounded,
                  size: 150,
                  color: Color(0xFF0D3B66),
                );
              },
            ),
            const SizedBox(height: 24),
            const Text(
              "My Kos",
              style: TextStyle(
                fontSize: 32,
                fontWeight: FontWeight.bold,
                color: Color(0xFF0D3B66),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
