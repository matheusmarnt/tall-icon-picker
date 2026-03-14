<p align="center">
  <img src="resources/images/tall-icon-picker-logo.png" alt="TALL Icon Picker Logo" width="500">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-11.0%2B-red" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Livewire-3.0-pink" alt="Livewire Version">
  <img src="https://img.shields.io/badge/TallStackUI-v2_opcional-emerald" alt="TallStackUI Version">
  <img src="https://img.shields.io/badge/Status-Internal_SMCTI_Use-orange" alt="Internal Use">
</p>

<p align="center">
  Um componente de seleção de ícones altamente otimizado e extensível para aplicações Laravel baseadas na <strong>TALL Stack</strong> (TailwindCSS, Alpine.js, Livewire, Laravel). Construído com foco em <strong>Clean Architecture</strong> e performance, este pacote delega a renderização para o motor do <a href="https://github.com/driesvints/blade-icons">Blade Icons</a> e oferece uma interface moderna compatível com ou sem <a href="https://tallstackui.com/">TallStackUI</a>.
</p>

---

## 🚀 Arquitetura e Recursos Principais

Diferente de seletores tradicionais que carregam arrays massivos em memória, o **TALL Icon Picker** foi projetado para operar com baixo consumo de recursos através de um padrão de *Service Layer*:

| Recurso | Descrição |
|---|---|
| **I/O Otimizado (`IconDiscoveryService`)** | A varredura dos arquivos `.svg` ocorre de forma isolada, lendo os artefatos diretamente do diretório `vendor` apenas quando requisitado. |
| **Lazy Loading & Paginação** | Milhares de ícones são processados em tempo real e paginados no backend, garantindo que o DOM do navegador e a payload do Livewire permaneçam extremamente leves. |
| **Dual UI Adapter** | Detecta automaticamente se o TallStackUI está instalado e renderiza os componentes correspondentes. Sem TallStackUI, utiliza componentes nativos Alpine.js/Tailwind com animações fluídas e design moderno. |
| **Extensibilidade (OCP)** | Aberto para extensão via arquivo de configuração (`config/tall-icon-picker.php`), permitindo a injeção de novas bibliotecas de ícones sem modificação do core do pacote. |
| **Batteries-Included** | Pré-configurado para 15+ coleções amplamente utilizadas (Lucide, Phosphor, FontAwesome, Heroicons, etc.). |
| **i18n** | Suporte nativo a múltiplos idiomas. Inclui `en` e `pt_BR` — extensível via publicação das traduções. |

---

## ⚙️ Pré-requisitos

| Dependência | Versão |
|---|---|
| PHP | `^8.2` |
| Laravel | `^11.0` ou `^12.0` |
| Livewire | `^3.0` |
| TallStackUI | `^2.0` *(opcional — detecção automática)* |

---

## 📦 Instalação

```bash
composer require matheusmarnt/tall-icon-picker
```

> **Nota:** O Composer instalará automaticamente `blade-ui-kit/blade-icons` e todas as bibliotecas de ícones vinculadas. O TallStackUI é uma dependência sugerida — se já estiver instalado no projeto, será utilizado automaticamente; caso contrário, os componentes nativos serão ativados.

---

## 🛠️ Configuração

O pacote funciona imediatamente (**plug-and-play**). Publique o arquivo de configuração para personalizar as bibliotecas indexadas e o adapter de UI:

```bash
php artisan vendor:publish --tag="tall-icon-picker-config"
```

O arquivo `config/tall-icon-picker.php` gerado expõe duas seções:

```php
return [

    /*
     | UI Adapter
     | 'auto'        — detecta TallStackUI via class_exists (padrão)
     | 'tallstackui' — força o uso dos componentes x-ts-*
     | 'native'      — força os componentes nativos Alpine.js/Tailwind
     */
    'ui' => env('TALL_ICON_PICKER_UI', 'auto'),

    'libraries' => [
        'lucide'    => ['package' => 'mallardduck/blade-lucide-icons', 'path' => 'resources/svg', 'label' => 'Lucide'],
        'heroicons' => ['package' => 'blade-ui-kit/blade-heroicons',   'path' => 'resources/svg', 'label' => 'Heroicons'],
        // ...
    ],

];
```

Para forçar o adapter via `.env`:

```dotenv
TALL_ICON_PICKER_UI=native      # sempre nativo
TALL_ICON_PICKER_UI=tallstackui # sempre TallStackUI
```

---

## 💻 Uso

### Via Componente Blade (Recomendado)

O wrapper Blade injeta o Livewire implicitamente e suporta o atributo `label`:

```html
<x-tall::icon-picker
    wire:model="icone_sistema"
    label="Selecione o ícone para o módulo"
/>
```

### Via Tag Livewire Direta

```html
<livewire:tall::icon-picker wire:model="icone_sistema" />
```

> **Under the Hood:** Ao selecionar um ícone, o componente Livewire despacha um evento `icon-picked` para a propriedade `$parentModel` mapeada, garantindo a sincronização reativa com o componente pai.

---

## 🖼️ Renderizando o Ícone Selecionado na View

O valor armazenado pela propriedade `wire:model` é o **identificador completo do ícone** no formato `{prefixo}-{nome}` (ex.: `lucide-home`, `heroicon-o-user`). Este identificador é diretamente compatível com o ecossistema [Blade Icons](https://blade-ui-kit.com/blade-icons).

### Via `<x-dynamic-component>` (Recomendado)

A forma mais idiomática — renderiza o SVG completo via Blade:

```html
{{-- $icone_sistema = 'lucide-home' --}}
<x-dynamic-component :component="$icone_sistema" class="w-6 h-6 text-gray-700" />
```

### Via helper `svg()`

O helper `svg()` provido pelo `blade-ui-kit/blade-icons` retorna o objeto SVG e permite renderização inline com `toHtml()`:

```php
// Em um componente Blade ou view Livewire
{!! svg($icone_sistema, 'w-6 h-6 text-indigo-500')->toHtml() !!}
```

### Via `@svg` Blade Directive

```html
@svg($icone_sistema, 'w-6 h-6')
```

### Exemplo Completo em um Componente Livewire

```php
// app/Livewire/ConfiguracaoModulo.php
class ConfiguracaoModulo extends Component
{
    public string $icone_sistema = '';

    public function render(): View
    {
        return view('livewire.configuracao-modulo');
    }
}
```

```html
{{-- resources/views/livewire/configuracao-modulo.blade.php --}}

{{-- Seletor --}}
<x-tall::icon-picker wire:model="icone_sistema" label="Ícone do módulo" />

{{-- Preview do ícone selecionado --}}
@if ($icone_sistema)
    <div class="mt-4 flex items-center gap-2 text-sm text-gray-600">
        <x-dynamic-component :component="$icone_sistema" class="w-5 h-5" />
        <span>{{ $icone_sistema }}</span>
    </div>
@endif
```

### Exibindo em Tabelas / Listagens

```html
{{-- $registro->icone = 'phosphor-house' --}}
<td class="flex items-center gap-2">
    @if ($registro->icone)
        <x-dynamic-component :component="$registro->icone" class="w-4 h-4 text-indigo-500" />
    @endif
    {{ $registro->nome }}
</td>
```

> **Atenção:** Certifique-se de que a biblioteca de ícones correspondente ao prefixo do ícone armazenado está instalada no projeto que irá renderizá-lo. Caso contrário, o `svg()` lançará uma exceção. Utilize `@if ($icone)` como guard antes de renderizar.

---

## 🎨 Customização Avançada de Views

Publique as views para sobrescrever o layout do seletor ou os estados vazios:

```bash
php artisan vendor:publish --tag="tall-icon-picker-views"
```

As views ficam em `resources/views/vendor/tall`. Os componentes UI adapter (`ui/drawer`, `ui/button`, `ui/select`, `ui/input`) também são publicados e podem ser customizados individualmente.

### Publicar somente as traduções

```bash
php artisan vendor:publish --tag="tall-icon-picker-translations"
```

Os arquivos de língua ficam em `lang/vendor/tall-icon-picker/{locale}/icon-picker.php`.

---

## 🔧 Solução de Problemas

**Ícones recém-instalados não aparecem**

```bash
php artisan view:clear
```

**SVG renderizando em tamanho desproporcional**

O renderizador aplica as classes passadas via `class=""`. Certifique-se de que os utilitários do Tailwind (`w-5 h-5`) estão sendo compilados — adicione o caminho do `vendor` ao `content` do `tailwind.config.js` se necessário.

**`x-dynamic-component` lançando `View not found`**

O componente Blade Icons para o ícone não está registrado. Verifique se a biblioteca correspondente está instalada via Composer e se seu `ServiceProvider` está sendo carregado.

**Componentes nativos sem animações**

Os componentes nativos utilizam `x-cloak` do Alpine.js. Adicione ao CSS global:

```css
[x-cloak] { display: none !important; }
```

---

## 🤝 Contribuindo (Equipe Interna)

Este pacote é mantido e utilizado exclusivamente nos projetos da equipe de desenvolvimento da **SMCTI**.

Antes de abrir um Pull Request:

- Identifique bugs ou oportunidades de refatoração abrindo uma **Issue** no repositório do pacote ou no template host.
- Respeite as diretrizes de **Clean Code** e a formatação **PSR-12** (via [Laravel Pint](https://laravel.com/docs/12.x/pint)).
- Todos os PRs devem passar no PHPStan (nível 6) e na suíte de testes Pest.
