import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

const Color _primary = Color(0xFF0D3B66);

class FilterBottomSheet extends StatefulWidget {
  final String initialLocation;
  final String initialType;
  final List<String> initialPriceRanges;
  final Function(String location, String type, List<String> priceRanges)
  onApply;
  final VoidCallback onClear;

  const FilterBottomSheet({
    super.key,
    required this.initialLocation,
    required this.initialType,
    required this.initialPriceRanges,
    required this.onApply,
    required this.onClear,
  });

  @override
  State<FilterBottomSheet> createState() => _FilterBottomSheetState();
}

class _FilterBottomSheetState extends State<FilterBottomSheet> {
  late TextEditingController _locationController;
  late String _selectedType;
  late List<String> _selectedPriceRanges;

  final List<String> _typeOptions = ['Putra', 'Putri', 'Campur'];
  final List<String> _priceOptions = [
    '< 500000',
    '500000 - 1000000',
    '1000000 - 1500000',
    '1500000 - 2000000',
  ];

  @override
  void initState() {
    super.initState();
    _locationController = TextEditingController(text: widget.initialLocation);
    _selectedType = widget.initialType;
    _selectedPriceRanges = List.from(widget.initialPriceRanges);
  }

  @override
  void dispose() {
    _locationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(
        top: 20,
        left: 20,
        right: 20,
        bottom: MediaQuery.of(context).viewInsets.bottom + 20,
      ),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                "Filter",
                style: GoogleFonts.inter(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Colors.black87,
                ),
              ),
              IconButton(
                icon: const Icon(Icons.close),
                color: Colors.grey[600],
                onPressed: () => Navigator.pop(context),
              ),
            ],
          ),
          const Divider(),
          const SizedBox(height: 10),

          // Scrollable Content
          Flexible(
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Section 1: Lokasi
                  Text(
                    "Lokasi",
                    style: GoogleFonts.inter(
                      fontWeight: FontWeight.bold,
                      fontSize: 15,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 10),
                  TextField(
                    controller: _locationController,
                    decoration: InputDecoration(
                      hintText: "Cari Lokasi",
                      hintStyle: GoogleFonts.inter(color: Colors.grey),
                      prefixIcon: const Icon(
                        Icons.location_on_outlined,
                        color: _primary,
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(10),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(10),
                        borderSide: const BorderSide(color: _primary),
                      ),
                      contentPadding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Section 2: Tipe Kos
                  Text(
                    "Tipe Kos",
                    style: GoogleFonts.inter(
                      fontWeight: FontWeight.bold,
                      fontSize: 15,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Wrap(
                    spacing: 10,
                    children: _typeOptions.map((type) {
                      final isSelected = _selectedType == type;
                      return ChoiceChip(
                        label: Text(
                          type,
                          style: GoogleFonts.inter(
                            color: isSelected ? Colors.white : Colors.black87,
                            fontWeight: isSelected
                                ? FontWeight.bold
                                : FontWeight.normal,
                          ),
                        ),
                        selected: isSelected,
                        selectedColor: _primary,
                        backgroundColor: Colors.grey.shade100,
                        onSelected: (selected) {
                          setState(() {
                            _selectedType = selected ? type : '';
                          });
                        },
                      );
                    }).toList(),
                  ),
                  const SizedBox(height: 20),

                  // Section 3: Rentang Harga
                  Text(
                    "Rentang Harga",
                    style: GoogleFonts.inter(
                      fontWeight: FontWeight.bold,
                      fontSize: 15,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Column(
                    children: _priceOptions.map((price) {
                      return CheckboxListTile(
                        title: Text(price, style: GoogleFonts.inter(fontSize: 14)),
                        value: _selectedPriceRanges.contains(price),
                        activeColor: _primary,
                        checkColor: Colors.white,
                        controlAffinity: ListTileControlAffinity.leading,
                        contentPadding: EdgeInsets.zero,
                        onChanged: (bool? value) {
                          setState(() {
                            if (value == true) {
                              _selectedPriceRanges.add(price);
                            } else {
                              _selectedPriceRanges.remove(price);
                            }
                          });
                        },
                      );
                    }).toList(),
                  ),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ),

          // Bottom Buttons
          Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: () {
                    setState(() {
                      _locationController.clear();
                      _selectedType = '';
                      _selectedPriceRanges.clear();
                    });
                    widget.onClear();
                    Navigator.pop(context);
                  },
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    side: const BorderSide(color: _primary),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: Text(
                    "Bersihkan",
                    style: GoogleFonts.inter(
                      color: _primary,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: ElevatedButton(
                  onPressed: () {
                    widget.onApply(
                      _locationController.text,
                      _selectedType,
                      _selectedPriceRanges,
                    );
                    Navigator.pop(context);
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: Text(
                    "Terapkan Filter",
                    style: GoogleFonts.inter(
                      color: Colors.white,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
