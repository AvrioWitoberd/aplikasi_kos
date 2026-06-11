import 'package:google_sign_in/google_sign_in.dart';
import 'package:flutter/foundation.dart';

class GoogleAuthService {
  final GoogleSignIn _googleSignIn = GoogleSignIn(
    scopes: ['email', 'profile'],
    // KOSONGKAN! Tidak ada clientId atau serverClientId
  );

  Future<Map<String, dynamic>> signInWithGoogle() async {
    try {
      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
      
      if (googleUser == null) {
        return {'status': 'cancelled', 'message': 'Login dibatalkan'};
      }
      
      final GoogleSignInAuthentication googleAuth = await googleUser.authentication;
      
      return {
        'status': 'success',
        'data': {
          'email': googleUser.email,
          'nama_lengkap': googleUser.displayName ?? googleUser.email.split('@')[0],
          'google_id': googleUser.id,
          'foto_profil': googleUser.photoUrl,
        }
      };
    } catch (e) {
      debugPrint('❌ Google Sign In Error: $e');
      return {'status': 'error', 'message': e.toString()};
    }
  }

  Future<void> signOut() async {
    await _googleSignIn.signOut();
  }
}