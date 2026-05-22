class Blog {
  final String title;
  final String content;
  final String category;
  final String imageUrl;
  final String date;

  Blog({
    required this.title,
    required this.content,
    required this.category,
    this.imageUrl = '',
    this.date = '',
  });
}
