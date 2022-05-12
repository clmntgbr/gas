<?php

namespace App\Service;

use App\Dto\GetMapGasStationsFiltersDto;
use App\Repository\GasServiceRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;

class GetMapGasStationsFiltersService
{
    public function __construct(
        private GasStationRepository $gasStationRepository,
        private GasTypeRepository    $gasTypeRepository,
        private GasServiceRepository $gasServiceRepository
    )
    {
    }

    public function get(): GetMapGasStationsFiltersDto
    {
        $getMapGasStationsFiltersDto = new GetMapGasStationsFiltersDto();

        $getMapGasStationsFiltersDto->postalCodes = $this->gasStationRepository->getPostalCodes();
        $getMapGasStationsFiltersDto->cities = $this->gasStationRepository->getCities();
        $getMapGasStationsFiltersDto->gasTypes = $this->gasTypeRepository->findGasTypeById();
        $getMapGasStationsFiltersDto->departments = $this->getDepartments();
        $getMapGasStationsFiltersDto->services = $this->gasServiceRepository->findGasServiceById();

        return $getMapGasStationsFiltersDto;
    }

    private function getDepartments()
    {
        return [
            ['name' => 'Ain', 'code' => '01'],
            ['name' => 'Aisne', 'code' => '02'],
            ['name' => 'Allier', 'code' => '03'],
            ['name' => 'Alpes-de-Haute-Provence', 'code' => '04'],
            ['name' => 'Hautes-alpes', 'code' => '05'],
            ['name' => 'Alpes-maritimes', 'code' => '06'],
            ['name' => 'Ardèche', 'code' => '07'],
            ['name' => 'Ardennes', 'code' => '08'],
            ['name' => 'Ariège', 'code' => '09'],
            ['name' => 'Aube', 'code' => '10'],
            ['name' => 'Aude', 'code' => '11'],
            ['name' => 'Aveyron', 'code' => '12'],
            ['name' => 'Bouches-du-Rhône', 'code' => '13'],
            ['name' => 'Calvados', 'code' => '14'],
            ['name' => 'Cantal', 'code' => '15'],
            ['name' => 'Charente', 'code' => '16'],
            ['name' => 'Charente-maritime', 'code' => '17'],
            ['name' => 'Cher', 'code' => '18'],
            ['name' => 'Corrèze', 'code' => '19'],
            ['name' => 'Corse-du-sud', 'code' => '2A'],
            ['name' => 'Haute-Corse', 'code' => '2B'],
            ['name' => 'Côte-d\'Or', 'code' => '21'],
            ['name' => 'Côtes-d\'Armor', 'code' => '22'],
            ['name' => 'Creuse', 'code' => '23'],
            ['name' => 'Dordogne', 'code' => '24'],
            ['name' => 'Doubs', 'code' => '25'],
            ['name' => 'Drôme', 'code' => '26'],
            ['name' => 'Eure', 'code' => '27'],
            ['name' => 'Eure-et-loir', 'code' => '28'],
            ['name' => 'Finistère', 'code' => '29'],
            ['name' => 'Gard', 'code' => '30'],
            ['name' => 'Haute-garonne', 'code' => '31'],
            ['name' => 'Gers', 'code' => '32'],
            ['name' => 'Gironde', 'code' => '33'],
            ['name' => 'Hérault', 'code' => '34'],
            ['name' => 'Ille-et-vilaine', 'code' => '35'],
            ['name' => 'Indre', 'code' => '36'],
            ['name' => 'Indre-et-loire', 'code' => '37'],
            ['name' => 'Isère', 'code' => '38'],
            ['name' => 'Jura', 'code' => '39'],
            ['name' => 'Landes', 'code' => '40'],
            ['name' => 'Loir-et-cher', 'code' => '41'],
            ['name' => 'Loire', 'code' => '42'],
            ['name' => 'Haute-loire', 'code' => '43'],
            ['name' => 'Loire-atlantique', 'code' => '44'],
            ['name' => 'Loiret', 'code' => '45'],
            ['name' => 'Lot', 'code' => '46'],
            ['name' => 'Lot-et-garonne', 'code' => '47'],
            ['name' => 'Lozère', 'code' => '48'],
            ['name' => 'Maine-et-loire', 'code' => '49'],
            ['name' => 'Manche', 'code' => '50'],
            ['name' => 'Marne', 'code' => '51'],
            ['name' => 'Haute-marne', 'code' => '52'],
            ['name' => 'Mayenne', 'code' => '53'],
            ['name' => 'Meurthe-et-moselle', 'code' => '54'],
            ['name' => 'Meuse', 'code' => '55'],
            ['name' => 'Morbihan', 'code' => '56'],
            ['name' => 'Moselle', 'code' => '57'],
            ['name' => 'Nièvre', 'code' => '58'],
            ['name' => 'Nord', 'code' => '59'],
            ['name' => 'Oise', 'code' => '60'],
            ['name' => 'Orne', 'code' => '61'],
            ['name' => 'Pas-de-calais', 'code' => '62'],
            ['name' => 'Puy-de-dôme', 'code' => '63'],
            ['name' => 'Pyrénées-atlantiques', 'code' => '64'],
            ['name' => 'Hautes-Pyrénées', 'code' => '65'],
            ['name' => 'Pyrénées-orientales', 'code' => '66'],
            ['name' => 'Bas-rhin', 'code' => '67'],
            ['name' => 'Haut-rhin', 'code' => '68'],
            ['name' => 'Rhône', 'code' => '69'],
            ['name' => 'Haute-saône', 'code' => '70'],
            ['name' => 'Saône-et-loire', 'code' => '71'],
            ['name' => 'Sarthe', 'code' => '72'],
            ['name' => 'Savoie', 'code' => '73'],
            ['name' => 'Haute-savoie', 'code' => '74'],
            ['name' => 'Paris', 'code' => '75'],
            ['name' => 'Seine-maritime', 'code' => '76'],
            ['name' => 'Seine-et-marne', 'code' => '77'],
            ['name' => 'Yvelines', 'code' => '78'],
            ['name' => 'Deux-sèvres', 'code' => '79'],
            ['name' => 'Somme', 'code' => '80'],
            ['name' => 'Tarn', 'code' => '81'],
            ['name' => 'Tarn-et-Garonne', 'code' => '82'],
            ['name' => 'Var', 'code' => '83'],
            ['name' => 'Vaucluse', 'code' => '84'],
            ['name' => 'Vendée', 'code' => '85'],
            ['name' => 'Vienne', 'code' => '86'],
            ['name' => 'Haute-vienne', 'code' => '87'],
            ['name' => 'Vosges', 'code' => '88'],
            ['name' => 'Yonne', 'code' => '89'],
            ['name' => 'Territoire de belfort', 'code' => '90'],
            ['name' => 'Essonne', 'code' => '91'],
            ['name' => 'Hauts-de-seine', 'code' => '92'],
            ['name' => 'Seine-Saint-Denis', 'code' => '93'],
            ['name' => 'Val-de-marne', 'code' => '94'],
            ['name' => 'Val-d\'Oise', 'code' => '95'],
        ];
    }
}