As informações configuradas no plugin, como os posts e páginas selecionados, e o número de telefone padrão, são armazenadas nas opções do WordPress. Isso é feito usando a função `update_option()` e `get_option()`. Essas informações **não são salvas diretamente em uma tabela do banco de dados do WordPress** específica para este plugin. Em vez disso, elas são salvas na tabela `wp_options`, que é uma tabela padrão do WordPress usada para armazenar configurações e opções.

Aqui está um resumo das configurações armazenadas na tabela `wp_options`:

1. **Posts Selecionados**: A lista de IDs dos posts selecionados é salva em uma opção chamada `whatsapp_share_selected_posts`.
   
   Exemplo de dado armazenado:
   - `whatsapp_share_selected_posts` = `[1, 2, 5, 8]` (IDs dos posts selecionados)

2. **Páginas Selecionadas**: A lista de IDs das páginas selecionadas é salva em uma opção chamada `whatsapp_share_selected_pages`.
   
   Exemplo de dado armazenado:
   - `whatsapp_share_selected_pages` = `[10, 12]` (IDs das páginas selecionadas)

3. **Número de Telefone Padrão**: O número de telefone que será usado no botão do WhatsApp é salvo em uma opção chamada `whatsapp_default_phone`.
   
   Exemplo de dado armazenado:
   - `whatsapp_default_phone` = `5511999999999` (número de telefone)

Essas informações ficam registradas na tabela `wp_options` do banco de dados WordPress. Para ver esses dados, você pode acessar diretamente o banco de dados usando uma ferramenta como phpMyAdmin, ou fazer consultas SQL diretamente:

```sql
SELECT * FROM wp_options WHERE option_name LIKE 'whatsapp%';
```

Esse comando SQL retornará todas as opções relacionadas ao plugin `Whats Post` que estão armazenadas na tabela `wp_options`.