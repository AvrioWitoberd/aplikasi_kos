import 'package:flutter/foundation.dart';

class AuthProvider with ChangeNotifier {
  bool _isLoggedIn = false;

  bool get isLoggedIn => _isLoggedIn;

  Future<bool> login(String email, String password) async {
    if (email == 'user@gmail.com' && password == '123456') {
      _isLoggedIn = true;
      notifyListeners();
      return true;
    }
    return false;
  }

  void logout() {
    _isLoggedIn = false;
    notifyListeners();
  }

  /// Guard helper for any action that requires authentication.
  ///
  /// If the user is logged in, [action] is executed immediately.
  /// If not, [onUnauthenticated] is called instead — the caller is
  /// responsible for showing a login prompt or navigating to LoginScreen.
  ///
  /// Usage (e.g. for favorite, rating):
  /// ```dart
  /// authProvider.performGuardedAction(
  ///   action: () => kosProvider.toggleFavorite(id),
  ///   onUnauthenticated: () => Navigator.push(context, loginRoute),
  /// );
  /// ```
  void performGuardedAction({
    required VoidCallback action,
    required VoidCallback onUnauthenticated,
  }) {
    if (_isLoggedIn) {
      action();
    } else {
      onUnauthenticated();
    }
  }
}
