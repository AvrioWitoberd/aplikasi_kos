import 'package:flutter/material.dart';

class UserModel {
  final int idUser;
  final String namaLengkap;
  final String email;
  final String? noHp;
  final String role;

  UserModel({
    required this.idUser,
    required this.namaLengkap,
    required this.email,
    this.noHp,
    required this.role,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    // Lebih fleksibel: bisa handle String atau int
    int userId = 0;
    final idUserValue = json['id_user'];
    if (idUserValue is int) {
      userId = idUserValue;
    } else if (idUserValue is String) {
      userId = int.tryParse(idUserValue) ?? 0;
    } else if (idUserValue is double) {
      userId = idUserValue.toInt();
    }
    
    return UserModel(
      idUser: userId,
      namaLengkap: json['nama_lengkap']?.toString() ?? '',
      email: json['email']?.toString() ?? '',
      noHp: json['no_hp']?.toString(),
      role: json['role']?.toString() ?? 'pencari',
    );
  }
}