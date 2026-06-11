class PrivacySection {
  final int id;
  final String judul;
  final String konten;

  PrivacySection({
    required this.id,
    required this.judul,
    required this.konten,
  });

  factory PrivacySection.fromJson(Map<String, dynamic> json) {
    return PrivacySection(
      id: json['id'] as int? ?? 0,
      judul: json['judul_section']?.toString() ?? '',
      konten: json['isi_konten']?.toString() ?? '',
    );
  }
}

class PrivacyData {
  final String introText;
  final List<PrivacySection> sections;

  PrivacyData({
    required this.introText,
    required this.sections,
  });
}