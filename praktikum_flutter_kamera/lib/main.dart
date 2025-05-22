import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:permission_handler/permission_handler.dart';

void main() => runApp(MyApp());

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Akses Kamera & Galeri',
      home: ImagePickerExample(),
      debugShowCheckedModeBanner: false,
    );
  }
}

class ImagePickerExample extends StatefulWidget {
  @override
  _ImagePickerExampleState createState() => _ImagePickerExampleState();
}

class _ImagePickerExampleState extends State<ImagePickerExample> {
  File? _image;
  final ImagePicker picker = ImagePicker();

  Future<void> _getImage(ImageSource source) async {
    bool permissionGranted = false;

    // Cek izin akses
    if (source == ImageSource.camera) {
      final status = await Permission.camera.request();
      permissionGranted = status.isGranted;
    } else {
      if (Platform.isAndroid) {
        final status = await Permission.storage.request();
        permissionGranted = status.isGranted;
      } else if (Platform.isIOS) {
        final status = await Permission.photos.request();
        permissionGranted = status.isGranted;
      }
    }

    // Kalau izin ditolak, keluar
    if (!permissionGranted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Izin tidak diberikan')),
      );
      return;
    }

    // Ambil gambar
    final pickedFile = await picker.pickImage(source: source);
    if (pickedFile != null) {
      setState(() {
        _image = File(pickedFile.path);
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Akses Kamera & Galeri')),
      body: Center(
        child: _image == null
            ? Text('Belum ada gambar')
            : Image.file(_image!),
      ),
      floatingActionButton: Column(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          FloatingActionButton(
            onPressed: () => _getImage(ImageSource.camera),
            tooltip: 'Ambil dari Kamera',
            child: Icon(Icons.camera_alt),
          ),
          SizedBox(height: 16),
          FloatingActionButton(
            onPressed: () => _getImage(ImageSource.gallery),
            tooltip: 'Ambil dari Galeri',
            child: Icon(Icons.photo_library),
          ),
        ],
      ),
    );
  }
}
