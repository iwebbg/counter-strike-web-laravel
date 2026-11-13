# Coding standards

[Български](../coding-standards.md) | [English](coding-standards.md)

Application classes and methods use explicit PHP types and short PHPDoc descriptions. Controllers coordinate HTTP concerns; socket protocol logic belongs behind query contracts; persistence belongs in models or actions. Multi-record writes use transactions. Every public endpoint and domain parser should receive a focused test. PHP changes are formatted with Pint.
