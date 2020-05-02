# Wikitext Parser

The custom implementation is there, as wikipedia does not offer a (in my mind) workable solution.
Theirs depend on way too many wikimedia components, to use in this project (requires the whole framework as of 2020).

It is a challenge supporting the complete feature-set. Many tokens are undocumented or have undocumented features (parameters, multiline start-end tags, etc.)

To get an overview of what is implemented, and what is missing - take a look below.

## Links

| Type | Supported | Description |
|-------|-----------|-------------|
| Internal | x | None |
| External | x | With caption support |

## Lists

| Type | Supported | Description |
|-------|-----------|-------------|
| Unordered | x | None |
| Ordered | x | None |
| Nested | x | None |

## Text

| Type | Supported | Description |
|-------|-----------|-------------|
| Bold | x | Wraps the text in HTML \<b\> tag |
| Italic | x | Wraps the text in HTML \<i\> tag |
| Paragraph | x | Wraps the text in HTML \<p\> tag |
