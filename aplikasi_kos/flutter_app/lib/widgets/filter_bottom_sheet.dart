import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class FilterBottomSheet extends StatefulWidget {
  final String initialSearch;
  final String initialTipe;
  final int initialMinHarga;
  final int initialMaxHarga;
  final Function(String, String, int, int) onApply;
  final VoidCallback onClear;

  const FilterBottomSheet({
    Key? key,
    required this.initialSearch,
    required this.initialTipe,
    required this.initialMinHarga,
    required this.initialMaxHarga,
    required this.onApply,
    required this.onClear,
  }) : super(key: key);

  @override
  State<FilterBottomSheet> createState() => _FilterBottomSheetState();
}

class _FilterBottomSheetState extends State<FilterBottomSheet> {
  late TextEditingController _searchController;
  late String _tipe;
  late int _minHarga;
  late int _maxHarga;
  late String _selectedRange;

  final List<Map<String, dynamic>> _priceRanges = [
    {'label': '< 500.000', 'min': 0, 'max': 500000, 'value': 'range1'},
    {'label': '500.000 - 1.000.000', 'min': 500000, 'max': 1000000, 'value': 'range2'},
    {'label': '1.000.000 - 1.500.000', 'min': 1000000, 'max': 1500000, 'value': 'range3'},
    {'label': '1.500.000 - 2.000.000', 'min': 1500000, 'max': 2000000, 'value': 'range4'},
    {'label': '> 2.000.000', 'min': 2000000, 'max': 999999999, 'value': 'range5'},
  ];

  @override
  void initState() {
    super.initState();
    _searchController = TextEditingController(text: widget.initialSearch);
    _tipe = widget.initialTipe;
    _minHarga = widget.initialMinHarga;
    _maxHarga = widget.initialMaxHarga;
    _selectedRange = _getSelectedRange(_minHarga, _maxHarga);
  }

  String _getSelectedRange(int minHarga, int maxHarga) {
    if (minHarga == 0 && maxHarga == 500000) return 'range1';
    if (minHarga == 500000 && maxHarga == 1000000) return 'range2';
    if (minHarga == 1000000 && maxHarga == 1500000) return 'range3';
    if (minHarga == 1500000 && maxHarga == 2000000) return 'range4';
    if (minHarga == 2000000 && maxHarga == 999999999) return 'range5';
    return '';
  }

  void _resetFilters() {
    setState(() {
      _searchController.clear();
      _tipe = '';
      _selectedRange = '';
      _minHarga = 0;
      _maxHarga = 999999999;
    });
    // Panggil onClear tapi jangan tutup modal
    widget.onClear();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      padding: const EdgeInsets.all(20),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Filter Pencarian',
                style: GoogleFonts.inter(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              TextButton(
                onPressed: _resetFilters,  // Panggil reset tanpa tutup modal
                child: Text('Reset', style: GoogleFonts.inter(color: Colors.red)),
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          // SEARCH (KOTA)
          Text('Cari Berdasarkan Kota', style: GoogleFonts.inter(fontWeight: FontWeight.w600)),
          const SizedBox(height: 8),
          TextField(
            controller: _searchController,
            decoration: InputDecoration(
              hintText: 'Masukkan nama kota...',
              hintStyle: GoogleFonts.inter(color: Colors.grey),
              prefixIcon: const Icon(Icons.location_city, size: 20, color: Colors.grey),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: BorderSide(color: Colors.grey.shade300),
              ),
              enabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: BorderSide(color: Colors.grey.shade300),
              ),
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: const BorderSide(color: Color(0xFF0D3B66)),
              ),
              contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
            ),
          ),
          const SizedBox(height: 16),
          
          // TIPE KOS
          Text('Tipe Kos', style: GoogleFonts.inter(fontWeight: FontWeight.w600)),
          const SizedBox(height: 8),
          Wrap(
            spacing: 8,
            children: ['Semua', 'Putra', 'Putri', 'Campur'].map((tipe) {
              final value = tipe == 'Semua' ? '' : tipe.toLowerCase();
              return FilterChip(
                label: Text(tipe),
                selected: _tipe == value,
                onSelected: (selected) {
                  setState(() {
                    _tipe = selected ? value : '';
                  });
                },
              );
            }).toList(),
          ),
          const SizedBox(height: 16),
          
          // RANGE HARGA (RADIO BUTTON)
          Text('Range Harga', style: GoogleFonts.inter(fontWeight: FontWeight.w600)),
          const SizedBox(height: 8),
          ..._priceRanges.map((range) {
            return RadioListTile<String>(
              title: Text(range['label'], style: GoogleFonts.inter()),
              value: range['value'],
              groupValue: _selectedRange,
              activeColor: const Color(0xFF0D3B66),
              contentPadding: EdgeInsets.zero,
              dense: true,
              onChanged: (value) {
                setState(() {
                  _selectedRange = value!;
                  _minHarga = range['min'];
                  _maxHarga = range['max'];
                });
              },
            );
          }).toList(),
          
          const SizedBox(height: 24),
          
          // TOMBOL TERAPKAN
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF0D3B66),
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              onPressed: () {
                widget.onApply(
                  _searchController.text,
                  _tipe,
                  _minHarga,
                  _maxHarga,
                );
                Navigator.pop(context);
              },
              child: Text(
                'Terapkan Filter',
                style: GoogleFonts.inter(color: Colors.white, fontWeight: FontWeight.w600),
              ),
            ),
          ),
        ],
      ),
    );
  }
}