import 'dart:io';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Login Google OAuth2 Desktop',
      home: LoginPage(),
    );
  }
}

class LoginPage extends StatefulWidget {
  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  String? _token;
  String? _userInfo;

  final int _callbackPort = 8081; // mesma porta que você usará no backend

  // Mini servidor HTTP para capturar token
  Future<String> _waitForToken() async {
    final server = await HttpServer.bind(InternetAddress.loopbackIPv4, _callbackPort);
    String? token;

    await for (var request in server) {
      final query = request.uri.queryParameters;
      token = query['token'];

      // Mostra uma página simples pro usuário
      request.response
        ..statusCode = 200
        ..headers.contentType = ContentType.html
        ..write('<h1>Login concluído! Pode fechar esta janela.</h1>')
        ..close();

      break; // fecha servidor depois de capturar
    }

    await server.close(force: true);
    return token!;
  }

  Future<void> _login() async {
    try {
      final loginUrl = "http://127.0.0.1:8081/SAE/index.php"; // URL do backend
      final tokenFuture = _waitForToken(); // inicia servidor para capturar token

      // Abre navegador para login
      if (await canLaunchUrl(Uri.parse(loginUrl))) {
        await launchUrl(Uri.parse(loginUrl));
      } else {
        throw Exception("Não foi possível abrir o navegador");
      }

      // Espera o token do callback
      final token = await tokenFuture;

      setState(() {
        _token = token;
      });

      // Chama API protegida com o token
      final response = await http.get(
        Uri.parse("http://127.0.0.1:8081/SAE/callback.php"), // ajuste sua API
        headers: {
          'Authorization': 'Bearer $token',
        },
      );

      setState(() {
        _userInfo = response.body;
      });
    } catch (e) {
      print("Erro no login: $e");
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text("Erro: $e")));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Login Google OAuth2 Desktop")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            ElevatedButton(
              onPressed: _login,
              child: Text("Login com Google"),
            ),
            SizedBox(height: 20),
            if (_token != null) Text("Token: $_token"),
            SizedBox(height: 20),
            if (_userInfo != null) Text("Dados da API: $_userInfo"),
          ],
        ),
      ),
    );
  }
}
