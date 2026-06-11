import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'providers/kos_provider.dart';
import 'providers/auth_provider.dart';
import 'providers/detail_kos_provider.dart';
import 'providers/blog_provider.dart';
import 'providers/privacy_provider.dart';
import 'providers/favorit_provider.dart';
import 'screens/splash_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // LOAD SESSION
  final authProvider = AuthProvider();
  await authProvider.init();
  
  runApp(MyApp(authProvider: authProvider));
}

class MyApp extends StatelessWidget {
  final AuthProvider authProvider;
  
  const MyApp({Key? key, required this.authProvider}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => KosProvider()),
        ChangeNotifierProvider(create: (_) => authProvider),
        ChangeNotifierProvider(create: (_) => DetailKosProvider()),
        ChangeNotifierProvider(create: (_) => BlogProvider()),
        ChangeNotifierProvider(create: (_) => PrivacyProvider()),
        ChangeNotifierProvider(create: (_) => FavoritProvider()),
      ],
      child: MaterialApp(
        title: 'My Kos',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          fontFamily: 'Poppins',
          scaffoldBackgroundColor: Colors.grey[50],
          useMaterial3: true,
        ),
        home: const SplashScreen(),
      ),
    );
  }
}