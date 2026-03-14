<p align="center">
  <img src="resources/images/tall-icon-picker-logo (1).png" alt="TALL Icon Picker Logo" width="500">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-12.0%2B-red" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Livewire-3.0-pink" alt="Livewire Version">
  <img src="https://img.shields.io/badge/TallStackUI-v2-emerald" alt="TallStackUI Version">
  <img src="https://img.shields.io/badge/Status-Internal_SMCTI_Use-orange" alt="Internal Use">
</p>

<p align="center">
  Um componente de seleção de ícones altamente otimizado e extensível para aplicações Laravel baseadas na <strong>TALL Stack</strong> (TailwindCSS, Alpine.js, Livewire, Laravel). Construído com foco em <strong>Clean Architecture</strong> e performance, este pacote delega a renderização para o motor do <a href="https://github.com/driesvints/blade-icons">Blade Icons</a> e integra-se nativamente à interface fluida do <a href="https://tallstackui.com/">TallStackUI</a>.
</p>

---

## 🚀 Arquitetura e Recursos Principais

Diferente de seletores tradicionais que carregam arrays massivos em memória, o **TALL Icon Picker** foi projetado para operar com baixo consumo de recursos através de um padrão de *Service Layer*:

| Recurso | Descrição |
|---|---|
| **I/O Otimizado (`IconDiscoveryService`)** | A varredura dos arquivos `.svg` ocorre de forma isolada, lendo os artefatos diretamente do diretório `vendor` apenas quando requisitado. |
| **Lazy Loading & Paginação** | Milhares de ícones são processados em tempo real e paginados no backend, garantindo que o DOM do navegador e a payload do Livewire permaneçam extremamente leves. |
| **Integração Nativa TallStackUI v2** | Herda o design system da aplicação host utilizando componentes como `x-ts-slide` e `x-ts-button` para manter a consistência visual. |
| **Extensibilidade (OCP)** | Aberto para extensão via arquivo de configuração (`config/tall-icon-picker.php`), permitindo a injeção de novas bibliotecas de ícones sem modificação do core do pacote. |
| **Batteries-Included** | Pré-configurado para resolver e baixar dependências de 15+ coleções amplamente utilizadas (Lucide, Phosphor, FontAwesome, Heroicons, etc.). |

---

## ⚙️ Pré-requisitos

| Dependência | Versão |
|---|---|
| PHP | `^8.2` |
| Laravel | `^11.0` ou `^12.0` |
| Livewire | `^3.0` |
| TallStackUI | `^2.0` (devidamente instalado e compilado no Tailwind) |

---

## 📦 Instalação

Como este pacote é uma biblioteca interna, instale-o diretamente no projeto alvo através do Composer:

```bash
composer require matheusmarnt/tall-icon-picker
```

> **Nota:** O Composer resolverá automaticamente a instalação do `blade-ui-kit/blade-icons` e de todas as bibliotecas de ícones atreladas ao pacote.

---

## 🛠️ Configuração

O pacote funciona imediatamente (**plug-and-play**). Contudo, você pode publicar o arquivo de configuração para customizar as bibliotecas indexadas no seletor:

```bash
php artisan vendor:publish --tag="tall-icon-picker-config"
```

O arquivo `config/tall-icon-picker.php` será gerado. Através dele, você controla quais bibliotecas de ícones o `IconDiscoveryService` deve varrer:

```php
// config/tall-icon-picker.php

return [
    'libraries' => [
        // Adicione, remova ou comente as bibliotecas conforme a necessidade do projeto
        'lucide'    => ['package' => 'mallardduck/blade-lucide-icons',  'path' => 'resources/svg', 'label' => 'Lucide'],
        'hugeicons' => ['package' => 'afatmustafa/blade-hugeicons',     'path' => 'resources/svg', 'label' => 'Hugeicons'],
        // ...
    ],
];
```

---

## 💻 Uso

A API do componente foi desenhada para ser limpa e seguir o padrão de convenção do ecossistema. O componente atende pelo namespace semântico `tall::`.

### Via Componente Blade (Recomendado)

O uso através do wrapper Blade injeta o Livewire implicitamente e fornece tratamento elegante para labels e atributos estáticos:

```html
<x-tall::icon-picker
    wire:model="icone_sistema"
    label="Selecione o ícone para o módulo"
/>
```

### Via Tag Livewire Direta

Caso prefira contornar o wrapper do Blade e instanciar o componente Livewire diretamente:

```html
<livewire:tall::icon-picker
    wire:model="icone_sistema"
/>
```

> **Under the Hood:** Ao selecionar um ícone, o Livewire interno despacha um evento `icon-picked` para a propriedade `$parentModel` mapeada, garantindo a sincronização reativa com o componente pai.

---

## 🎨 Customização Avançada de Views

Se o design system do projeto exigir alterações estruturais no modal de seleção (o slide do TallStackUI) ou nos estados vazios, extraia as views para o diretório da aplicação host:

```bash
php artisan vendor:publish --tag="tall-icon-picker-views"
```

As views estarão acessíveis para modificação em `resources/views/vendor/tall`.

---

## 🔧 Solução de Problemas

**Ícones recém-instalados não aparecem**

O Laravel faz cache de views e componentes. Ao modificar a configuração, execute:

```bash
php artisan view:clear
```

**SVG renderizando em tamanho desproporcional**

O renderizador utiliza o padrão do Blade Icons (`w-5 h-5`). Certifique-se de que os estilos utilitários do Tailwind não estão sofrendo sobreposição de classes do escopo global.

---

## 🤝 Contribuindo (Equipe Interna)

Este pacote é mantido e utilizado exclusivamente nos projetos da equipe de desenvolvimento da **SMCTI**.

Antes de abrir um Pull Request:

- Identifique bugs ou oportunidades de refatoração abrindo uma **Issue** no repositório do pacote ou no template host.
- Respeite as diretrizes de **Clean Code** e a formatação **PSR-12** (via [Laravel Pint](https://laravel.com/docs/12.x/pint)).