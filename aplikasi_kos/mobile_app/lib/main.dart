import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:mobile_app/providers/kos_provider.dart';
import 'package:mobile_app/providers/blog_provider.dart';
import 'package:mobile_app/providers/auth_provider.dart';
import 'package:mobile_app/core/routes/app_routes.dart';
void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => KosProvider()),
        ChangeNotifierProvider(create: (_) => BlogProvider()),
        ChangeNotifierProvider(create: (_) => AuthProvider()),
      ],
      child: const MyKosApp(),
    ),
  );
}

class MyKosApp extends StatelessWidget {
  const MyKosApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'My Kos',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF0D3B66)),
        primaryColor: const Color(0xFF0D3B66),
        scaffoldBackgroundColor: Colors.white,
        useMaterial3: true,
        textTheme: GoogleFonts.interTextTheme(Theme.of(context).textTheme),
      ),
      initialRoute: AppRoutes.splash,
      onGenerateRoute: AppRoutes.generateRoute,
    );
  }
}
