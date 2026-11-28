# ARP Spoofing 기반 MITM 공격 분석 및 L2 계층 탐지·방어

> VMware 기반 가상 네트워크 환경에서 ARP Spoofing과 MITM 공격의 원리를 분석하고, L2 계층의 탐지 및 방어 방법을 검증하는 프로젝트입니다.

---

## 1. 프로젝트 소개

### 1.1 프로젝트 목적

- ARP Spoofing 및 MITM 공격의 동작 원리 분석
- 네트워크 패킷 분석을 통한 공격 징후 확인
- L2 계층의 탐지 및 방어 방법 연구
- 방어 설정 전후의 네트워크 상태 비교

### 1.2 실습 환경

모든 실습은 VMware 기반의 격리된 가상 네트워크에서 진행합니다.

- 가상 머신: 총 4대
- 가상 네트워크: VMnet1, VMnet2
- 웹 서버: Ubuntu Server + PHP
- 클라이언트: Windows
- 네트워크 분석 환경: Kali Linux
- 게이트웨이: Ubuntu Desktop

---

## 2. 개발 및 실습 환경

### 2.1 사용 소프트웨어

| 구분 | 사용 프로그램 |
|:---|:---|
| 가상화 | VMware Workstation |
| 클라이언트 OS | Windows |
| 분석 OS | Kali Linux |
| 게이트웨이 OS | Ubuntu Desktop |
| 서버 OS | Ubuntu Server |
| 웹 서버 | PHP |
| 패킷 분석 | Wireshark |
| 버전 관리 | Git / GitHub |

### 2.2 가상 머신 구성

| VM | 운영체제 | 역할 | 네트워크 어댑터 |
|:---|:---|:---|:---|
| Windows Client | Windows | 피해자 클라이언트 및 로그인 페이지 접속 | VMnet1 |
| Kali Linux | Kali Linux | 공격자 및 네트워크 관찰 | VMnet1 |
| Ubuntu Gateway | Ubuntu Desktop | 네트워크 게이트웨이 | VMnet1, VMnet2 |
| Ubuntu Server | Ubuntu Server | PHP 웹 서버 | VMnet2 |

### 2.3 Ubuntu Gateway 사양

| 항목 | 설정 |
|:---|:---|
| CPU | 2 Core |
| RAM | 4 GB |
| Disk | 20 GB |
| Network Adapter | 2개 |

### 2.4 Ubuntu Server 사양

| 항목 | 설정 |
|:---|:---|
| CPU | 2 Core |
| RAM | 4 GB |
| Disk | 20 GB |
| Network Adapter | 1개 |
| Web Server | PHP |
| Web Port | 8080 |

※ Windows와 Kali Linux의 사양은 팀원별 VMware 환경에 맞춰 구성합니다.

---

## 3. 네트워크 구성

### 3.1 네트워크 구조

본 프로젝트에서는 두 개의 Host-only 가상 네트워크를 사용합니다.

| 네트워크 | 용도 | 서브넷 |
|:---|:---|:---|
| VMnet1 | 클라이언트 측 네트워크 | 192.168.112.0/24 |
| VMnet2 | 서버 측 네트워크 | 192.168.42.0/24 |

### 3.2 VMware Network Adapter 연결

| 가상 머신 | Adapter 1 | Adapter 2 |
|:---|:---|:---|
| Windows Client | VMnet1 | - |
| Kali Linux | VMnet1 | - |
| Ubuntu Gateway | VMnet1 | VMnet2 |
| Ubuntu Server | VMnet2 | - |

VMnet1과 VMnet2는 서로 다른 Host-only 네트워크로 구성합니다.

### 3.3 IP 주소 구성

현재 실습 환경에서 확인한 IP 주소입니다.

| 가상 머신 | 인터페이스 | IP 주소 |
|:---|:---|:---|
| Windows Client | Ethernet0 | 192.168.112.129 |
| Kali Linux | eth0 | 192.168.112.132 |
| Ubuntu Gateway | ens33 | 192.168.112.128 |
| Ubuntu Gateway | ens37 | 192.168.42.130 |
| Ubuntu Server | ens33 | 192.168.42.129 |

> 위 주소는 현재 실습 환경에서 확인한 값입니다. DHCP를 사용하는 경우 팀원별로 IP 주소가 달라질 수 있으므로 실제 할당 주소를 확인해야 합니다.

---

## 4. Windows Client 네트워크 설정

Windows Client는 VMware의 VMnet1 네트워크에 연결하며, IPv4 주소를 수동으로 지정합니다.

### 4.1 VMware 네트워크 어댑터 설정

1. VMware Workstation에서 Windows Client VM을 선택합니다.
2. `Edit virtual machine settings`를 클릭합니다.
3. `Network Adapter`를 선택합니다.
4. `Custom: Specific virtual network`를 선택합니다.
5. `VMnet1`을 지정합니다.
6. `Connected`와 `Connect at power on`을 체크합니다.

### 4.2 Windows IPv4 설정

Windows에서 다음 경로로 이동합니다.

`제어판 → 네트워크 및 공유 센터 → 어댑터 설정 변경`

1. Ethernet0을 마우스 오른쪽 버튼으로 클릭합니다.
2. `속성(Properties)`을 선택합니다.
3. `Internet Protocol Version 4 (TCP/IPv4)`를 선택합니다.
4. `속성(Properties)`을 클릭합니다.
5. `다음 IP 주소 사용`을 선택합니다.

다음 값을 입력합니다.

| 항목 | 설정값 |
|:---|:---|
| IP 주소 | 192.168.112.129 |
| 서브넷 마스크 | 255.255.255.0 |
| 기본 게이트웨이 | 192.168.112.128 |
| 기본 설정 DNS | 실습 환경에 맞게 설정 |
| 보조 DNS | 선택 사항 |

6. 설정을 저장하고 창을 닫습니다.

### 4.3 네트워크 설정 확인

Windows CMD를 실행하고 다음 명령어를 입력합니다.

```cmd
ipconfig /all
```

정상적으로 설정되었다면 다음과 같이 표시됩니다.

```text
IPv4 주소       : 192.168.112.129
서브넷 마스크   : 255.255.255.0
기본 게이트웨이 : 192.168.112.128
DHCP 사용       : 아니요
```

### 4.4 네트워크 연결 확인

Windows CMD에서 다음 명령어를 실행합니다.

```cmd
ping 192.168.112.128
```

게이트웨이와의 기본적인 연결 상태를 확인할 수 있습니다.

---

## 5. Ubuntu Server 네트워크 설정

Ubuntu Server는 DHCP를 통해 IP 주소를 자동으로 할당받으며, 기본 게이트웨이는 Ubuntu Gateway의 서버 측 인터페이스 주소를 사용하도록 설정합니다.

### 5.1 현재 네트워크 정보

| 항목 | 설정값 |
|:---|:---|
| 네트워크 인터페이스 | ens33 |
| IPv4 할당 방식 | DHCP |
| 현재 할당 IP | 192.168.42.129 |
| 서브넷 마스크 | 255.255.255.0 |
| 기본 게이트웨이 | 192.168.42.130 |

※ 기본 게이트웨이 주소는 Ubuntu Gateway의 서버 측 IP 주소에 맞춰 설정합니다.

### 5.2 Netplan 설정 파일 확인

```bash
ls -l /etc/netplan/
```

현재 실습 환경에서 사용하는 설정 파일:

```text
/etc/netplan/00-installer-config.yaml
```

### 5.3 기존 설정 백업

```bash
sudo cp /etc/netplan/00-installer-config.yaml \
/etc/netplan/00-installer-config.yaml.bak
```

### 5.4 Netplan 설정 수정

```bash
sudo nano /etc/netplan/00-installer-config.yaml
```

기존 설정을 다음과 같이 수정합니다.

```yaml
network:
  version: 2
  ethernets:
    ens33:
      dhcp4: true
      dhcp6: true
      dhcp4-overrides:
        use-routes: false
      dhcp6-overrides:
        use-routes: false
      routes:
        - to: default
          via: 192.168.42.130
```

**설정 내용**

- `dhcp4: true`: IPv4 주소를 DHCP로 자동 할당
- `dhcp6: true`: IPv6 주소를 DHCP로 자동 할당
- `use-routes: false`: DHCP에서 전달하는 경로 정보를 사용하지 않음
- `routes`: 기본 게이트웨이를 직접 지정

### 5.5 설정 적용

설정 문법을 확인합니다.

```bash
sudo netplan generate
```

오류가 없다면 다음 명령어를 실행합니다.

```bash
sudo netplan try
```

정상적으로 적용되면 설정을 확정합니다.

### 5.6 설정 결과 확인

```bash
ip -br addr
ip route
```

정상적인 설정의 주요 결과는 다음과 같습니다.

```text
ens33 UP 192.168.42.129/24
default via 192.168.42.130 dev ens33
```

IP 주소는 DHCP 환경에 따라 달라질 수 있습니다.

---

## 6. Ubuntu Server 웹 서버 구성

### 6.1 PHP 설치

Ubuntu Server 터미널에서 다음 명령어를 실행합니다.

```bash
sudo apt update
sudo apt install -y php
```

### 6.2 GitHub 저장소 가져오기

```bash
git clone https://github.com/sonamoo0407/security.git
```

저장소 디렉터리로 이동합니다.

```bash
cd security
```

### 6.3 PHP 서버 실행

PHP 파일이 위치한 디렉터리에서 다음 명령어를 실행합니다.

```bash
cd ~/security/server/ubuntu
php -S 0.0.0.0:8080
```

서버가 정상적으로 실행되면 다음 주소에서 접속할 수 있습니다.

```text
http://192.168.42.129:8080
```

> 실제 접속 주소는 Ubuntu Server에 할당된 IP 주소에 맞춰 변경합니다.

### 6.4 서버 실행 확인

PHP 서버가 실행되면 다음과 비슷한 메시지가 출력됩니다.

```text
PHP Development Server
(http://0.0.0.0:8080) started
```

---

## 7. 네트워크 상태 확인

각 VM에서 다음 명령어를 사용하여 네트워크 상태를 확인합니다.

### 7.1 Windows Client

```cmd
ipconfig /all
```

### 7.2 Kali Linux

```bash
ip -br addr
ip route
```

### 7.3 Ubuntu Gateway

```bash
ip -br addr
ip route
sysctl net.ipv4.ip_forward
```

### 7.4 Ubuntu Server

```bash
ip -br addr
ip route
```

---

## 8. 웹 서버 접속 및 요청 확인

### 8.1 Windows에서 로그인 페이지 접속

Windows 브라우저에서 Ubuntu Server의 IP 주소와 포트를 입력합니다.

```text
http://192.168.42.129:8080
```

### 8.2 PHP 서버 요청 로그 확인

Ubuntu Server에서 PHP 서버를 실행한 터미널을 통해 HTTP 요청을 확인할 수 있습니다.

정상적인 로그인 요청이 도착하면 다음과 같은 로그가 출력됩니다.

```text
192.168.112.129:58079 Accepted
[200]: POST /auth.php
```

### 8.3 로그 확인 항목

| 로그 항목 | 의미 |
|:---|:---|
| 클라이언트 IP | 요청을 보낸 장치 |
| GET | 페이지 조회 요청 |
| POST | 로그인 정보 제출 요청 |
| /auth.php | 로그인 요청을 처리하는 PHP 파일 |
| HTTP 200 | HTTP 요청 처리 성공 |

> HTTP 200은 요청 처리가 정상적으로 완료되었다는 의미이며, 로그인 인증 성공 여부와는 구분해야 합니다.

---

## 9. 네트워크 분석 및 방어 검증

### 9.1 주요 분석 항목

- 정상적인 클라이언트와 서버 간 통신 확인
- ARP 테이블 및 IP-MAC 매핑 상태 관찰
- Wireshark를 이용한 패킷 분석
- L2 계층 방어 설정 검증
- 방어 전후 정상 통신 유지 여부 비교
- 탐지 결과 및 분석 자료 정리

### 9.2 검증 항목

| 검증 항목 | 확인 내용 |
|:---|:---|
| ARP 상태 | IP-MAC 매핑 상태 |
| 네트워크 통신 | 클라이언트와 서버 간 통신 여부 |
| HTTP 요청 | GET / POST 요청 확인 |
| 서버 응답 | HTTP 응답 코드 |
| 방어 효과 | 비정상 ARP 매핑에 대한 방어 여부 |
| 정상 통신 | 방어 이후 정상 서비스 유지 여부 |

---

