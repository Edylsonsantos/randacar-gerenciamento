# 🚌 Randacard – Sistema de Gestão de Viaturas e Paragens

**Randacard** é um sistema web desenvolvido com PHP e MySQL para o registo, consulta e gestão de viaturas, paragens e utilizadores. O sistema também inclui recursos para visualização em mapa e registro de históricos de uso.

## 🚀 Funcionalidades

- Autenticação de utilizadores (login/logout)
- Cadastro de utilizadores
- Cadastro de viaturas
- Registro de paragens com localização
- Consulta e pesquisa de viaturas
- Histórico de movimentações ou atividades
- Visualização de mapa interativo

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP (sem framework)
- **Frontend**: HTML, CSS (arquivo `c_u.css`)
- **Banco de Dados**: MySQL
- **Mapa**: Suporte para integração com APIs de mapas (ex: Leaflet ou Google Maps)

## 🧱 Estrutura do Banco de Dados (Proposta)

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('administrador', 'operador') DEFAULT 'operador',
    telefone VARCHAR(20),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE viaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(50) NOT NULL UNIQUE,
    modelo VARCHAR(100),
    marca VARCHAR(100),
    ano INT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE paragens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    descricao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE historico_viaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    viatura_id INT,
    usuario_id INT,
    descricao TEXT,
    data_acao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (viatura_id) REFERENCES viaturas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

## 📂 Estrutura do Projeto

```
randacard/
├── index.php
├── login.php
├── logout.php
├── cadastro_usuario.php
├── cadastro_viatura.php
├── adicionar_paragem.php
├── historico.php
├── historico_viatura.php
├── map.html
├── pesquisar_viatura.php
├── ver_dados_usuario.php
├── viatura.php
├── c_u.css
```

## ▶️ Como Usar

1. Copie os arquivos para a pasta do servidor local (como `htdocs` no XAMPP)
2. Crie a base de dados no MySQL e importe o script com as tabelas sugeridas acima
3. Ajuste as credenciais de conexão ao banco de dados nos arquivos PHP
4. Acesse `http://localhost/randacard` no navegador

## 👨‍💻 Autor

**Edilson Luis Temporario Dos Santos**  
📞 Telefone/WhatsApp: +258 86 995 5418  
📧 E-mail: edylsondossantos02@gmail.com / edilsson.santos@labore.co.mz

## 📄 Licença

Este projeto é livre para fins acadêmicos, educacionais ou comerciais. Modificações são bem-vindas.
