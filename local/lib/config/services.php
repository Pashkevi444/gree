<?php

declare(strict_types=1);

use Gree\Contract\Repository\BrandRepositoryInterface;
use Gree\Contract\Repository\CatalogRepositoryInterface;
use Gree\Contract\Repository\HomeRepositoryInterface;
use Gree\Contract\Repository\OfferRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\BrandServiceInterface;
use Gree\Contract\Service\BreadcrumbsServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\HomeServiceInterface;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Contract\Service\TranslationLoaderInterface;
use Gree\Contract\Service\TranslatorServiceInterface;
use Gree\Controller\BlogController;
use Gree\Controller\BrandController;
use Gree\Controller\CatalogController;
use Gree\Controller\HomeController;
use Gree\Controller\LanguageController;
use Gree\Controller\ProductController;
use Gree\Repository\BrandRepository;
use Gree\Repository\CatalogRepository;
use Gree\Repository\HomeRepository;
use Gree\Repository\OfferRepository;
use Gree\Repository\ProductRepository;
use Gree\Repository\TranslationRepository;
use Gree\Service\BrandService;
use Gree\Service\BreadcrumbsService;
use Gree\Service\CatalogService;
use Gree\Service\HomeService;
use Gree\Service\LanguageService;
use Gree\Service\TranslatorService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

// ─── i18n ─────────────────────────────────────────────────────────────────
$container->register(LanguageService::class)->setPublic(true);
$container->setAlias(LanguageServiceInterface::class, LanguageService::class)->setPublic(true);

// ─── Repositories ─────────────────────────────────────────────────────────
$container
    ->register(BrandRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(BrandRepositoryInterface::class, BrandRepository::class)->setPublic(true);

$container
    ->register(HomeRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(HomeRepositoryInterface::class, HomeRepository::class)->setPublic(true);

$container
    ->register(OfferRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(OfferRepositoryInterface::class, OfferRepository::class)->setPublic(true);

$container
    ->register(ProductRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->addArgument(new Reference(OfferRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(ProductRepositoryInterface::class, ProductRepository::class)->setPublic(true);

$container
    ->register(CatalogRepository::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(CatalogRepositoryInterface::class, CatalogRepository::class)->setPublic(true);

// ─── Translator stack ─────────────────────────────────────────────────────
$container->register(TranslationRepository::class)->setPublic(true);
$container->setAlias(TranslationLoaderInterface::class, TranslationRepository::class)->setPublic(true);

$container
    ->register(TranslatorService::class)
    ->addArgument(new Reference(TranslationLoaderInterface::class))
    ->setPublic(true);
$container->setAlias(TranslatorServiceInterface::class, TranslatorService::class)->setPublic(true);

// ─── Domain services ──────────────────────────────────────────────────────
$container
    ->register(HomeService::class)
    ->addArgument(new Reference(HomeRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(HomeServiceInterface::class, HomeService::class)->setPublic(true);

$container
    ->register(CatalogService::class)
    ->addArgument(new Reference(ProductRepositoryInterface::class))
    ->addArgument(new Reference(CatalogRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(CatalogServiceInterface::class, CatalogService::class)->setPublic(true);

$container
    ->register(BrandService::class)
    ->addArgument(new Reference(BrandRepositoryInterface::class))
    ->setPublic(true);
$container->setAlias(BrandServiceInterface::class, BrandService::class)->setPublic(true);

$container
    ->register(BreadcrumbsService::class)
    ->addArgument(new Reference(TranslatorServiceInterface::class))
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);
$container->setAlias(BreadcrumbsServiceInterface::class, BreadcrumbsService::class)->setPublic(true);

// ─── Controllers ──────────────────────────────────────────────────────────
$container
    ->register(HomeController::class)
    ->addArgument(new Reference(HomeServiceInterface::class))
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->setPublic(true);

$container
    ->register(BrandController::class)
    ->addArgument(new Reference(BrandServiceInterface::class))
    ->setPublic(true);

$container->register(BlogController::class)->setPublic(true);

$container
    ->register(CatalogController::class)
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->addArgument(new Reference(BreadcrumbsServiceInterface::class))
    ->setPublic(true);

$container
    ->register(ProductController::class)
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->addArgument(new Reference(BreadcrumbsServiceInterface::class))
    ->setPublic(true);

$container
    ->register(LanguageController::class)
    ->addArgument(new Reference(LanguageServiceInterface::class))
    ->setPublic(true);

$container->compile();

return $container;
