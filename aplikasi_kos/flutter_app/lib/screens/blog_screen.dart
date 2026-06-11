import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/blog_provider.dart';
import '../widgets/blog_card.dart';
import 'detail_blog_screen.dart';

class BlogScreen extends StatefulWidget {
  const BlogScreen({Key? key}) : super(key: key);

  @override
  State<BlogScreen> createState() => _BlogScreenState();
}

class _BlogScreenState extends State<BlogScreen> {
  String _selectedKategori = '';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<BlogProvider>().loadBlog();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text(
          'Blog',
          style: GoogleFonts.inter(fontWeight: FontWeight.bold, color: Colors.white),
        ),
        backgroundColor: const Color(0xFF0D3B66),
        elevation: 0,
        automaticallyImplyLeading: false, // TAMBAHKAN INI
      ),
      body: Consumer<BlogProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.filteredBlogList.isEmpty) {
            return const Center(
              child: CircularProgressIndicator(color: Color(0xFF0D3B66)),
            );
          }
          
          if (provider.hasError) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                  const SizedBox(height: 16),
                  Text(
                    'Gagal memuat data',
                    style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    provider.errorMessage,
                    style: GoogleFonts.inter(fontSize: 12, color: Colors.grey[600]),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => provider.loadBlog(),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0D3B66),
                    ),
                    child: Text('Coba Lagi', style: GoogleFonts.inter(color: Colors.white)),
                  ),
                ],
              ),
            );
          }
          
          if (provider.filteredBlogList.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.article_outlined, size: 64, color: Colors.grey[350]),
                  const SizedBox(height: 16),
                  Text(
                    'Belum ada artikel',
                    style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w600, color: Colors.grey[500]),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Belum ada artikel yang ditemukan.',
                    style: GoogleFonts.inter(fontSize: 13, color: Colors.grey[400]),
                  ),
                ],
              ),
            );
          }
          
          return Column(
            children: [
              // Search & Filter Bar
              Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  children: [
                    Expanded(
                      flex: 2,
                      child: Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.grey.shade300),
                        ),
                        child: TextField(
                          onChanged: (value) {
                            provider.updateSearchQuery(value);
                          },
                          decoration: InputDecoration(
                            hintText: "Cari judul berita...",
                            hintStyle: GoogleFonts.inter(color: Colors.grey, fontSize: 13),
                            prefixIcon: const Icon(Icons.search, color: Colors.grey, size: 20),
                            border: InputBorder.none,
                            contentPadding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.grey.shade300),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<String>(
                            value: _selectedKategori.isEmpty ? null : _selectedKategori,
                            hint: Text(
                              'All',
                              style: GoogleFonts.inter(fontSize: 13, color: Colors.grey),
                            ),
                            isExpanded: true,
                            icon: Icon(Icons.keyboard_arrow_down, size: 18, color: Colors.grey[600]),
                            items: [
                              const DropdownMenuItem<String>(
                                value: null,
                                child: Text('All'),
                              ),
                              ...provider.uniqueKategori.map((kategori) {
                                return DropdownMenuItem<String>(
                                  value: kategori,
                                  child: Text(
                                    kategori,
                                    style: GoogleFonts.inter(fontSize: 13),
                                  ),
                                );
                              }),
                            ],
                            onChanged: (value) {
                              setState(() {
                                _selectedKategori = value ?? '';
                              });
                              provider.updateKategori(value ?? '');
                            },
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              
              // Gunakan ListView.builder saja untuk menghindari overflow
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  itemCount: provider.filteredBlogList.length,
                  itemBuilder: (context, index) {
                    final blog = provider.filteredBlogList[index];
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 16),
                      child: BlogCard(
                        blog: blog,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => DetailBlogScreen(blog: blog),
                            ),
                          );
                        },
                      ),
                    );
                  },
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}