<?php

declare(strict_types=1);

use Gree\Contract\Repository\BrandRepositoryInterface;
use Gree\Contract\Repository\HomeRepositoryInterface;
use Gree\Contract\Repository\ProductRepositoryInterface;
use Gree\Contract\Service\BrandServiceInterface;
use Gree\Contract\Service\CatalogServiceInterface;
use Gree\Contract\Service\HomeServiceInterface;
use Gree\Controller\BlogController;
use Gree\Controller\BrandController;
use Gree\Controller\CatalogController;
use Gree\Controller\HomeController;
use Gree\Controller\ProductController;
use Gree\Repository\BrandRepository;
use Gree\Repository\HomeRepository;
use Gree\Repository\ProductRepository;
use Gree\Service\BrandService;
use Gree\Service\CatalogService;
use Gree\Service\HomeService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

// Repositories — D7 handles caching internally via setCacheTtl()
$container
    ->register(BrandRepository::class, BrandRepository::class)
    ->setPublic(true);

$container
    ->setAlias(BrandRepositoryInterface::class, BrandRepository::class)
    ->setPublic(true);

$container
    ->register(HomeRepository::class, HomeRepository::class)
    ->setPublic(true);

$container
    ->setAlias(HomeRepositoryInterface::class, HomeRepository::class)
    ->setPublic(true);

$container
    ->register(ProductRepository::class, ProductRepository::class)
    ->setPublic(true);

$container
    ->setAlias(ProductRepositoryInterface::class, ProductRepository::class)
    ->setPublic(true);

// Services
$container
    ->register(HomeService::class, HomeService::class)
    ->addArgument(new Reference(HomeRepositoryInterface::class))
    ->setPublic(true);

$container
    ->setAlias(HomeServiceInterface::class, HomeService::class)
    ->setPublic(true);

$container
    ->register(CatalogService::class, CatalogService::class)
    ->addArgument(new Reference(ProductRepositoryInterface::class))
    ->setPublic(true);

$container
    ->setAlias(CatalogServiceInterface::class, CatalogService::class)
    ->setPublic(true);

$container
    ->register(BrandService::class, BrandService::class)
    ->addArgument(new Reference(BrandRepositoryInterface::class))
    ->setPublic(true);

$container
    ->setAlias(BrandServiceInterface::class, BrandService::class)
    ->setPublic(true);

// Controllers
$container
    ->register(HomeController::class, HomeController::class)
    ->addArgument(new Reference(HomeServiceInterface::class))
    ->setPublic(true);

$container
    ->register(BrandController::class, BrandController::class)
    ->addArgument(new Reference(BrandServiceInterface::class))
    ->setPublic(true);

$container
    ->register(BlogController::class, BlogController::class)
    ->setPublic(true);

$container
    ->register(CatalogController::class, CatalogController::class)
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->setPublic(true);

$container
    ->register(ProductController::class, ProductController::class)
    ->addArgument(new Reference(CatalogServiceInterface::class))
    ->setPublic(true);

$container->compile();

return $container;
