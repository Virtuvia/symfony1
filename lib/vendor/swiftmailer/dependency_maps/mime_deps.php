<?php

require dirname(__FILE__) . '/../mime_types.php';

Swift_DependencyContainer::getInstance()

  -> register('properties.charset')
  -> asValue('utf-8')

  -> register('mime.grammar')
  -> asSharedInstanceOf('Swift_Mime_Grammar')

  -> register('mime.message')
  -> asNewInstanceOf('Swift_Mime_SimpleMessage')
  -> withDependencies([
      'mime.headerset',
      'mime.qpcontentencoder',
      'cache',
      'mime.grammar',
      'properties.charset',
  ])

  -> register('mime.part')
  -> asNewInstanceOf('Swift_Mime_MimePart')
  -> withDependencies([
      'mime.headerset',
      'mime.qpcontentencoder',
      'cache',
      'mime.grammar',
      'properties.charset',
  ])

  -> register('mime.attachment')
  -> asNewInstanceOf('Swift_Mime_Attachment')
  -> withDependencies([
      'mime.headerset',
      'mime.base64contentencoder',
      'cache',
      'mime.grammar',
  ])
  -> addConstructorValue($swift_mime_types)

  -> register('mime.embeddedfile')
  -> asNewInstanceOf('Swift_Mime_EmbeddedFile')
  -> withDependencies([
      'mime.headerset',
      'mime.base64contentencoder',
      'cache',
      'mime.grammar',
  ])
  -> addConstructorValue($swift_mime_types)

  -> register('mime.headerfactory')
  -> asNewInstanceOf('Swift_Mime_SimpleHeaderFactory')
  -> withDependencies([
      'mime.qpheaderencoder',
      'mime.rfc2231encoder',
      'mime.grammar',
      'properties.charset',
  ])

  -> register('mime.headerset')
  -> asNewInstanceOf('Swift_Mime_SimpleHeaderSet')
  -> withDependencies(['mime.headerfactory', 'properties.charset'])

  -> register('mime.qpheaderencoder')
  -> asNewInstanceOf('Swift_Mime_HeaderEncoder_QpHeaderEncoder')
  -> withDependencies(['mime.charstream'])

  -> register('mime.charstream')
  -> asNewInstanceOf('Swift_CharacterStream_NgCharacterStream')
  -> withDependencies(['mime.characterreaderfactory', 'properties.charset'])

  -> register('mime.bytecanonicalizer')
  -> asSharedInstanceOf('Swift_StreamFilters_ByteArrayReplacementFilter')
  -> addConstructorValue([[0x0D, 0x0A], [0x0D], [0x0A]])
  -> addConstructorValue([[0x0A], [0x0A], [0x0D, 0x0A]])

  -> register('mime.characterreaderfactory')
  -> asSharedInstanceOf('Swift_CharacterReaderFactory_SimpleCharacterReaderFactory')

  -> register('mime.qpcontentencoder')
  -> asNewInstanceOf('Swift_Mime_ContentEncoder_QpContentEncoder')
  -> withDependencies(['mime.charstream', 'mime.bytecanonicalizer'])

  -> register('mime.7bitcontentencoder')
  -> asNewInstanceOf('Swift_Mime_ContentEncoder_PlainContentEncoder')
  -> addConstructorValue('7bit')
  -> addConstructorValue(true)

  -> register('mime.8bitcontentencoder')
  -> asNewInstanceOf('Swift_Mime_ContentEncoder_PlainContentEncoder')
  -> addConstructorValue('8bit')
  -> addConstructorValue(true)

  -> register('mime.base64contentencoder')
  -> asSharedInstanceOf('Swift_Mime_ContentEncoder_Base64ContentEncoder')

  -> register('mime.rfc2231encoder')
  -> asNewInstanceOf('Swift_Encoder_Rfc2231Encoder')
  -> withDependencies(['mime.charstream'])

;

unset($swift_mime_types);
