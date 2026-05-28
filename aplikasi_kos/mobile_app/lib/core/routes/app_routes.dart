import 'package:flutter/material.dart';
import 'package:mobile_app/models/blog.dart';
import 'package:mobile_app/models/kos.dart';
import 'package:mobile_app/views/auth/login_screen.dart';
import 'package:mobile_app/views/auth/register_screen.dart';
import 'package:mobile_app/views/auth/role_selection_screen.dart';
import 'package:mobile_app/views/blog/blog_detail_screen.dart';
import 'package:mobile_app/views/home/kos_detail_screen.dart';
import 'package:mobile_app/views/home/privacy_screen.dart';
import 'package:mobile_app/views/main_navigation.dart';
import 'package:mobile_app/views/splash/splash_screen.dart';

class AppRoutes {
  static const String splash = '/';
  static const String main = '/main';
  static const String roleSelection = '/role-selection';
  static const String login = '/login';
  static const String register = '/register';
  static const String kosDetail = '/kos-detail';
  static const String blogDetail = '/blog-detail';
  static const String privacy = '/privacy';

  static Route<dynamic> generateRoute(RouteSettings settings) {
    switch (settings.name) {
      case splash:
        return MaterialPageRoute(builder: (_) => const SplashScreen());
      case main:
        return MaterialPageRoute(builder: (_) => const MainNavigationScreen());
      case roleSelection:
        return MaterialPageRoute(builder: (_) => const RoleSelectionScreen());
      case login:
        return MaterialPageRoute(builder: (_) => const LoginScreen());
      case register:
        return MaterialPageRoute(builder: (_) => const RegisterScreen());
      case privacy:
        return MaterialPageRoute(builder: (_) => const PrivacyScreen());
      case kosDetail:
        if (settings.arguments is Kos) {
          final kos = settings.arguments as Kos;
          return MaterialPageRoute(
            builder: (_) => KosDetailScreen(kos: kos),
          );
        }
        return _errorRoute();
      case blogDetail:
        if (settings.arguments is Blog) {
          final blog = settings.arguments as Blog;
          return MaterialPageRoute(
            builder: (_) => BlogDetailScreen(blog: blog),
          );
        }
        return _errorRoute();
      default:
        return _errorRoute();
    }
  }

  static Route<dynamic> _errorRoute() {
    return MaterialPageRoute(
      builder: (_) => Scaffold(
        appBar: AppBar(title: const Text('Error')),
        body: const Center(
          child: Text('Halaman tidak ditemukan / Parameter tidak valid'),
        ),
      ),
    );
  }
}
