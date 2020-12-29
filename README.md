annotations-to-json-schema
==========================

Generate JSON Schema documents based on annotations in PHP files.

Version at developer stage.


## Motivation

Many people (e.g. me) use PHP to transfer objects between client and server (DTO).
The object must be serialized (ex. to XML or JSON) before it can be sent.
After receiving such an object, the server performs a deserialization.
For this it needs to know how to perform the conversion.
If the client and the server use the same metadata, there is usually no problem.
Worse if the structure of the serialized data does not match the server's metadata, e.g. you will get a string instead of the expected array of objects.
In this case, the deserializer will report an exception and stop further processing.
The most popular serializers (Symfony, JMS) will return very brief information about the cause of the problem.

## Solution

The solution may be to validate the JSON document before it goes to deserialization.
To validate documents we can use JSON Schema documents.
This package allows to create JSON Schema documents based on existing PHP entities.

## Usage

Base entities should utilize PSR-4 compatible paths.

TO BE CONTINUED...

#### Standalone
```shell script
Usage:
  convert2jschema [options] [--] <className>

Arguments:
  className                                  Entry class

Options:
  -c, --config[=CONFIG]                      Config file
  -r, --root-namespace[=ROOT-NAMESPACE]      RootNamespace
  -o, --destination-path[=DESTINATION-PATH]  Output directory
```

Sample `config.yaml`:
```yaml
# config.yml

a2jschema:
  rootNamespace: TKusy\JSchema\Tests\Assets\
  idPrefix: http://json-schema.org/schema/

  destination:
    path: ~
    pathTemplate: "%s.schema.json"
```
#### Samples
`bin/convert2jschema -c config.yaml TKusy\\JSchema\\Tests\\Assets\\Main\\Referral` - use config yaml to generate schemas as 'siblings' of entities.

`bin/convert2jschema --root-namespace=TKusy\\JSchema\\Tests\\Assets\\ --destination-path=./output/ Main\\Referral` - write schemas to `./output` dir.

#### In PHP
@TO DO...
