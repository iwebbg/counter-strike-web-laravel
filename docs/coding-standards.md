# Стандарти за програмиране

[Български](coding-standards.md) | [English](en/coding-standards.md)

Класовете и методите на приложението използват explicit PHP типове и кратки PHPDoc описания. Контролерите координират HTTP действията; socket protocol логиката стои зад query contracts; persistence логиката е в модели или actions. Операциите с няколко записа използват transactions. Всеки публичен endpoint и domain parser трябва да има фокусиран тест. PHP промените се форматират с Pint.
